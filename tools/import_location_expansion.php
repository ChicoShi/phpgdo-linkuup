<?php
declare(strict_types=1);
/** Local-only, additive import. Default: read-only validation. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$root=dirname(__DIR__,3);
require $root.'/protected/config.php';
require $root.'/GDO7.php';
if (!in_array(GDO_DB_HOST,['localhost','127.0.0.1','::1'],true)) { throw new RuntimeException('Only a local database is allowed.'); }
$db=\GDO\DB\Database::init()->connect();
$bars=in_array('--bars',$argv,true);
$batch2=in_array('--batch2',$argv,true);
$batch3=in_array('--batch3',$argv,true);
if((int)$bars+(int)$batch2+(int)$batch3>1)throw new RuntimeException('Select one batch only.');
$dataset=$bars?'bar-expansion':($batch3?'location-expansion-3':($batch2?'location-expansion-2':'location-expansion'));
$doc=json_decode(file_get_contents(dirname(__DIR__).'/data/'.$dataset.'/locations.json'),true,512,JSON_THROW_ON_ERROR);
$entries=$doc['entries'];
if(count($entries)!==100 || count(array_unique(array_column($entries,'key')))!==100) throw new RuntimeException('Expected 100 unique locations.');
$apply=in_array('--apply-local',$argv,true);
$counts=['inserted'=>0,'existing'=>0,'validated'=>0];
$keys=[];
foreach($entries as $e){
 if($bars && (!in_array($e['category'],[3,4],true) || empty($e['building_source_url']))) throw new RuntimeException('Expected sourced bar/pub.');
 if(mb_strlen($e['name'])>64 || !preg_match('/^osm-(node|way)-\d+$/',$e['key']) || !preg_match('/^\d{5}$/',$e['zip']) || !$e['street'] || $e['lat']<51 || $e['lat']>54 || $e['lng']<9 || $e['lng']>12 || ($e['chat_radius_km']<0.002 || $e['chat_radius_km']>0.5 || !$e['polygon'])) throw new RuntimeException('Invalid source entry: '.$e['key']);
 if(!empty($e['website']) && preg_match('/[^\x20-\x7E]/',$e['website'])) throw new RuntimeException('URL must be IDNA/percent encoded: '.$e['key']);
 $counts['validated']++;
}
$find=$db->prepare('SELECT room_id FROM lup_room WHERE room_info LIKE ?');
$duplicate=$db->prepare('SELECT r.room_id FROM lup_room r LEFT JOIN gdo_address a ON a.address_id=r.room_address WHERE (r.room_name=? AND a.address_city=?) OR (a.address_city=? AND a.address_street=?)');
$address=$db->prepare('INSERT INTO gdo_address (address_name,address_street,address_zip,address_city,address_country) VALUES (?,?,?,?,?)');
$room=$db->prepare('INSERT INTO lup_room (room_name,room_info,room_color,room_category,room_pos_lat,room_pos_lng,room_view,room_radius,room_www,room_address,room_rating,room_needs_vip,room_has_mira,room_show_distance,room_enabled,room_active) VALUES (?,?,?,?,?,?,?,?,?,?,0,0,0,1,1,1)');
if($apply)$db->begin_transaction();
try {
 foreach($entries as $e){
  $match='%['.$e['key'].']%';$find->execute([$match]);$found=$find->get_result()->fetch_assoc();
  if($found){$counts['existing']++;$keys[$e['key']]=(int)$found['room_id'];continue;}
  $duplicate->execute([$e['name'],$e['city'],$e['city'],$e['street']]);
  if($duplicate->get_result()->num_rows)throw new RuntimeException('Duplicate address/name requires review: '.$e['key']);
  if(!$apply)continue;
  $address->execute([$e['name'],$e['street'],$e['zip'],$e['city'],'DE']);$aid=$db->insert_id;
  $info='Quelle: OpenStreetMap contributors (ODbL). ['.$e['key'].'] '.$e['source_url'].' | Lokaler Test: Kreis innerhalb OSM-Gebäude; GPS-Abweichung möglich; Betreiber ungeprüft.';
  $color=match($e['category']) {3,4,5,14=>'#E8B47F',11=>'#E6A4DF',default=>'#91BFF3'};
  $room->execute([$e['name'],$info,$color,$e['category'],$e['lat'],$e['lng'],$e['view_km'],$e['chat_radius_km'],$e['website']??$e['source_url'],$aid]);
  $keys[$e['key']]=$db->insert_id;$counts['inserted']++;
 }
 if($apply)$db->commit();
} catch(Throwable $e){if($apply)$db->rollback();throw $e;}
echo json_encode(['mode'=>$apply?'local-import':'dry-run','counts'=>$counts,'room_mapping'=>$keys],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
