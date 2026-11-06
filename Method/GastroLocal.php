<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;
use GDO\Core\GDT;
use GDO\Core\Application;
use GDO\Core\GDT_JSON;
use GDO\Core\GDT_Object;
use GDO\Core\MethodAjax;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Commerce\Ordering;
use GDO\LinkUUp\Commerce\Access;
use GDO\LinkUUp\Commerce\PilotStore;
use GDO\LinkUUp\Commerce\TestWallet;
use GDO\Session\GDO_Session;
use GDO\User\GDO_User;

/** Authenticated localhost pilot. No payment provider and no production deployment. */
final class GastroLocal extends MethodAjax
{
    private static function pilotEnabled(bool $topup=false): bool
    {
        // Only trusted server configuration can opt into this isolated pilot.
        // Never infer permission from Host/Origin or other client headers.
        return defined('GDO_LINKUUP_GASTRO_LOCAL_PILOT')
            && constant('GDO_LINKUUP_GASTRO_LOCAL_PILOT') === true
            && (!$topup || (defined('GDO_LINKUUP_GASTRO_TEST_TOPUP')
                && constant('GDO_LINKUUP_GASTRO_TEST_TOPUP') === true));
    }
    private static function localAppRequest(): bool
    {
        $host=$_SERVER['HTTP_HOST']??'';
        if ($host==='app.localhost') return true;
        // The local /backend proxy forwards Host: localhost. Require the actual
        // app origin/referrer as well; never accept arbitrary forwarded headers.
        if ($host!=='localhost') return false;
        if (($_SERVER['HTTP_ORIGIN']??'')==='http://app.localhost') return true;
        $ref=parse_url($_SERVER['HTTP_REFERER']??'');
        return is_array($ref) && ($ref['scheme']??'')==='http' && ($ref['host']??'')==='app.localhost' && !isset($ref['port']);
    }
    public function gdoParameters(): array { return [GDT_Object::make('room')->table(LUP_Room::table())->notNull()]; }
    public function hasPermission(GDO_User $user, string &$error, array &$args): bool
    {
        $allowed=self::pilotEnabled() && in_array($_SERVER['REMOTE_ADDR']??'', ['127.0.0.1','::1'],true)
            && self::localAppRequest()
            && in_array($_SERVER['HTTP_ORIGIN']??'', ['', 'http://app.localhost'],true) && $user->isMember() && $user->gdoVar('user_deleted')===null
            && GDO_Session::instance() && $this->gdoParameterValue('room');
        if (!$allowed) $error='err_not_allowed';
        return (bool)$allowed;
    }
    public function execute(): GDT
    {
        header('Cache-Control: no-store');
        try {
            if (!self::pilotEnabled()) throw new \RuntimeException('Lokaler Gastro-Pilot ist deaktiviert',403);
            $user=GDO_User::current();$room=LUP_Room::table()->getById($this->gdoParameterValue('room')->getID());
            if (!$room || !$user->isMember() || $user->gdoVar('user_deleted')!==null) throw new \RuntimeException('Anmeldung erforderlich');
            $uid=(string)$user->getID();$rid=(string)$room->getID();
            if (!ctype_digit($rid)) throw new \RuntimeException('Ungültige Location');
            $owner=(string)$room->getOwnerID();$manage=$room->isOwner($user);
            $token=GDO_Session::get('gastro_local_csrf');
            if (!is_string($token) || strlen($token)!==64) { $token=bin2hex(random_bytes(32));GDO_Session::set('gastro_local_csrf',$token); if(!GDO_Session::commit())throw new \RuntimeException('Sitzung nicht speicherbar',403); }
            $method=$_SERVER['REQUEST_METHOD']??'GET';$r=[];
            if($method==='POST') {
                if(($_SERVER['HTTP_ORIGIN']??'')!=='http://app.localhost' || (int)($_SERVER['CONTENT_LENGTH']??0)>200000) throw new \RuntimeException('Ungültige Anfrage');
                $body=file_get_contents('php://input',false,null,0,200001);
                if($body===false || strlen($body)>200000)throw new \RuntimeException('Anfrage zu groß');
                $r=json_decode($body,true,32,JSON_THROW_ON_ERROR);
                if(!is_array($r))throw new \RuntimeException('Ungültige Anfrage');
            } elseif($method!=='GET')throw new \RuntimeException('Methode nicht erlaubt');
            $action=$r['action']??'state';
            if ($action==='topup-test' && !self::pilotEnabled(true)) throw new \RuntimeException('Testaufladung ist deaktiviert',403);
            $inactive=$room->isDisabled() || $room->gdoValue('room_deleted')!==null;
            if (!is_string($action)) throw new \RuntimeException('Ungültige Aktion');
            Access::check($user->isMember() && $user->gdoVar('user_deleted')===null, $manage, $inactive, $action, $method, $_SERVER['HTTP_ORIGIN']??'', $token, (string)($_SERVER['HTTP_X_GASTRO_CSRF']??''));
            $result=PilotStore::run('/var/lib/linkuup-gastro-local', $action!=='state', function(array &$store) use($rid,$room,$owner,$action,$r,$uid,$manage,$token,$inactive): array {
                // Re-read authority after acquiring the pilot lock: cached session objects
                // must not authorize a deleted account or a transferred location.
                $freshUser=GDO_User::table()->select()->where('user_id='.GDO_User::quoteS($uid))->uncached()->first()->exec()->fetchObject();
                $room=LUP_Room::table()->select()->where('room_id='.LUP_Room::quoteS($rid))->uncached()->first()->exec()->fetchObject();
                if (!$freshUser || !$room) throw new \RuntimeException('Zugriff nicht verfügbar',403);
                $manage=$room->isOwner($freshUser);$owner=(string)$room->getOwnerID();
                $inactive=$room->isDisabled() || $room->gdoValue('room_deleted')!==null;
                Access::check($freshUser->isMember() && $freshUser->gdoVar('user_deleted')===null, $manage, $inactive, $action, $_SERVER['REQUEST_METHOD']??'GET', $_SERVER['HTTP_ORIGIN']??'', $token, (string)($_SERVER['HTTP_X_GASTRO_CSRF']??''));
                $store['rooms'][$rid] ??= ['venue'=>['id'=>$rid,'name'=>$room->getName(),'owner'=>$owner],'revision'=>1,'menu'=>[],'orders'=>[],'tableCount'=>20,'menuReady'=>false,'tablesReady'=>false,'orderingEnabled'=>false];
                $s=&$store['rooms'][$rid];
                // Authority is always resolved from GDO, never from browser or old persisted owner.
                $s['venue']=['id'=>$rid,'name'=>$room->getName(),'owner'=>$owner];
                if(in_array($action,['save-menu','save-tables','publication'],true)&&($r['revision']??null)!==$s['revision'])throw new \RuntimeException('Stand geändert. Bitte aktualisieren.');
                $result=[];
                switch($action) {
                    case 'state':$result=$s;$result['wallet']=TestWallet::summary($store['ledger'],$uid);$result['orders']=array_values(array_filter($s['orders'],fn($o)=>$o['venue']===$rid&&($manage||$o['guest']===$uid)));unset($result['venue']['owner']);$result['canManage']=$manage;$result['csrf']=$token;$result['localPilot']=true;if($inactive)$result['orderingEnabled']=false;if(!$manage&&!$result['orderingEnabled'])$result['menu']=[];break;
                    case 'save-menu':$s['menu']=Ordering::menu($r['rows']??[]);$s['menuReady']=true;if(!array_filter($s['menu'],fn($i)=>$i['available']))$s['orderingEnabled']=false;$s['revision']++;break;
                    case 'save-tables':if(!is_int($r['count']??null))throw new \RuntimeException('Ganzzahl erforderlich');Ordering::tables($s,$uid,$r['count']);$s['revision']++;break;
                    case 'publication':if(!is_bool($r['enabled']??null))throw new \RuntimeException('Freigabe fehlt');Ordering::publication($s,$uid,$r['enabled']);$s['revision']++;break;
                    case 'topup-test':if(!is_int($r['cents']??null))throw new \RuntimeException('Betrag erforderlich');$result=TestWallet::topup($store['ledger'],$uid,$r['cents'],(string)($r['key']??''));break;
                    case 'place':$before=count($s['orders']);$result=Ordering::place($s,$uid,$r);$new=count($s['orders'])>$before;if($new||($result['walletFunded']??false)){TestWallet::reserve($store['ledger'],$uid,$rid.':'.$result['id'],$result['totalCents']);if($new){$s['orders'][$before]['walletFunded']=true;$result['walletFunded']=true;}}break;
                    case 'transition':$result=Ordering::transition($s,$uid,(string)($r['id']??''),(string)($r['target']??''));$settlement=['delivered'=>'captured','cancelled'=>'released','refunded'=>'refunded'][$result['status']]??null;if($settlement)TestWallet::settle($store['ledger'],$result['guest'],$rid.':'.$result['id'],$settlement);break;
                    case 'message':Ordering::message($s,$uid,(string)($r['id']??''),(string)($r['text']??''));break;
                    default:throw new \RuntimeException('Aktion nicht verfügbar');
                }
                return $result;
            });
            return GDT_JSON::make()->value($result);
        } catch(\Throwable $e) {Application::setResponseCode(in_array($e->getCode(),[403,405,409],true)?$e->getCode():400);return GDT_JSON::make()->value(['error'=>$e instanceof \RuntimeException||$e instanceof \InvalidArgumentException?$e->getMessage():'Anfrage konnte nicht verarbeitet werden']);}
    }
}
