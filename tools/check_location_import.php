<?php
declare(strict_types=1);
/** Read-only local database -> GDO -> WebSocket position round-trip check. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$_SERVER['REQUEST_METHOD'] = 'GET';
$root = dirname(__DIR__, 3);
require $root . '/protected/config.php';
require $root . '/GDO7.php';
if (!in_array(GDO_DB_HOST, ['localhost', '127.0.0.1', '::1'], true)) {
    throw new RuntimeException('Only a local database is allowed.');
}
\GDO\DB\Database::init()->connect();
$loader = \GDO\Core\ModuleLoader::instance();
$loader->loadModulesCache();
$loader->initModules();
$entries = json_decode(file_get_contents(dirname(__DIR__) . '/data/location-expansion-3/locations.json'), true, 512, JSON_THROW_ON_ERROR)['entries'];
$includeBinary = in_array('--binary', $argv, true);
$rows = [];
foreach ($entries as $entry) {
    $room = \GDO\LinkUUp\LUP_Room::table()->select()
        ->where('room_info LIKE ' . \GDO\LinkUUp\LUP_Room::quoteS('%[' . $entry['key'] . ']%'))
        ->first()->exec()->fetchObject();
    if (!$room) { throw new RuntimeException('Missing imported room: ' . $entry['key']); }
    $position = $room->gdoColumn('room_pos')->gdo($room)->renderBinary();
    if (strlen($position) !== 8) { throw new RuntimeException('Unexpected position wire format.'); }
    $binary = unpack('flat/flng', $position);
    $address = $room->getAddressOrBlank();
    // Exercise complete room/address serialization without sending a message.
    $command = new \GDO\LinkUUp\Websocket\LUPWS_Room();
    $payload = $command->gdoToBinary($room) . $command->gdoToBinary($address);
    if (!$payload) { throw new RuntimeException('Empty room payload.'); }
    $row = [
        'key' => $entry['key'], 'room_id' => $room->getID(),
        'lat' => $room->getLat(), 'lng' => $room->getLng(),
        'binary_lat' => $binary['lat'], 'binary_lng' => $binary['lng'],
        'radius_km' => $room->getRadius(), 'name' => $room->getName(),
        'category' => $room->getCategory(), 'website' => $room->getWww(),
        'info' => $room->getInfo(), 'address' => $address->getGDOVars(),
        'payload_bytes' => strlen($payload),
    ];
    if ($includeBinary) { $row['payload_base64'] = base64_encode($payload); }
    $rows[] = $row;
}
echo json_encode(['count' => count($rows), 'rows' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
