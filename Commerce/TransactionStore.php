<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Commerce;

/** MariaDB/InnoDB pilot store. Never migrates tables or imports old JSON on request. */
final class TransactionStore
{
    public static function run(\mysqli $db, array $tables, array $identity, bool $write, callable $operation): array
    {
        $begun=false;
        try {
            foreach (['session','user','room','store'] as $name) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/D', $tables[$name]??'')) throw new \RuntimeException('Ungültiges Speicherschema');
                $stmt=$db->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?');
                $stmt->bind_param('s',$tables[$name]);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();$stmt->close();
                if (strtoupper($row['ENGINE']??'')!=='INNODB') throw new \RuntimeException('Transaktionaler Gastro-Speicher ist nicht eingerichtet',503);
            }
            // Do not implicitly commit a transaction owned by another GDO caller.
            if ((int)$db->query('SELECT @@in_transaction')->fetch_row()[0]!==0) throw new \RuntimeException('Verschachtelte Transaktion nicht erlaubt',503);
            $db->begin_transaction();$begun=true;
            // Fixed lock order: session -> user -> room -> shared pilot ledger.
            $session=self::row($db,$tables['session'],'sess_id',(string)$identity['session']);
            if (!$session || empty($identity['token']) || (string)$session['sess_user']!==(string)$identity['user']
                || !hash_equals((string)$session['sess_token'],(string)$identity['token'])) throw new \RuntimeException('Sitzung abgelaufen',403);
            if (($identity['maxAge']??0)>0) {
                $last=strtotime(($session['sess_time']??'').' UTC');
                if ($last===false || $last<time()-(int)$identity['maxAge']) throw new \RuntimeException('Sitzung abgelaufen',403);
            }
            $user=self::row($db,$tables['user'],'user_id',(string)$identity['user']);
            $room=self::row($db,$tables['room'],'room_id',(string)$identity['room']);
            if (!$user || $user['user_type']!=='member' || $user['user_deleted']!==null || !$room) throw new \RuntimeException('Zugriff nicht verfügbar',403);
            $data=$session['sess_data']===null?[]:@unserialize($session['sess_data'],['allowed_classes'=>false]);
            if (!is_array($data)) throw new \RuntimeException('Sitzung ungültig',403);
            if (!is_string($data['gastro_local_csrf']??null) || strlen($data['gastro_local_csrf'])!==64) {
                if (empty($identity['allowCsrfInit'])) throw new \RuntimeException('Sitzung neu öffnen',403);
                $data['gastro_local_csrf']=bin2hex(random_bytes(32));
                $serialized=serialize($data);$sid=(string)$identity['session'];
                $stmt=$db->prepare('UPDATE `'.$tables['session'].'` SET sess_data=? WHERE sess_id=?');
                $stmt->bind_param('ss',$serialized,$sid);$stmt->execute();$stmt->close();
            }
            if (!empty($identity['requireCsrf']) && !hash_equals($data['gastro_local_csrf'],(string)($identity['csrf']??''))) throw new \RuntimeException('Sitzung geändert',403);
            $record=self::row($db,$tables['store'],'store_id','1');
            if (!$record) throw new \RuntimeException('Gastro-Speicher ist nicht initialisiert',503);
            $store=json_decode($record['store_data'],true,64,JSON_THROW_ON_ERROR);
            if (!is_array($store['rooms']??null)||!is_array($store['ledger']??null)) throw new \RuntimeException('Ungültiger Speicher');
            $result=$operation($store,['user'=>$user,'room'=>$room,'csrf'=>$data['gastro_local_csrf']]);
            if ($write) {
                $json=json_encode($store,JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE);
                if (strlen($json)>16000000) throw new \RuntimeException('Pilot-Speichergrenze erreicht',503);
                $stmt=$db->prepare('UPDATE `'.$tables['store'].'` SET store_data=? WHERE store_id=1');
                $stmt->bind_param('s',$json);$stmt->execute();$stmt->close();
            }
            if (!$db->commit()) throw new \RuntimeException('Buchung nicht bestätigt',503);
            $begun=false;return $result;
        } catch (\mysqli_sql_exception $e) {
            // No SQL or request values in client-visible error messages.
            throw new \RuntimeException('Datenbankvorgang fehlgeschlagen; Stand vor erneutem Versuch prüfen',503);
        } finally {if ($begun)$db->rollback();}
    }
    private static function row(\mysqli $db,string $table,string $key,string $id): ?array
    {
        $stmt=$db->prepare('SELECT * FROM `'.$table.'` WHERE `'.$key.'`=? FOR UPDATE');
        $stmt->bind_param('s',$id);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();$stmt->close();return $row;
    }
}
