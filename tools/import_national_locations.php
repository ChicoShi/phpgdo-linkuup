<?php
declare(strict_types=1);
/**
 * Additive local import of explicitly reviewed national candidates.
 * Input: schema_version=1, entries[]. Required fields: key, name, city, street,
 * zip, category, source_url, import_ready (boolean). Optional website.
 * Active entries additionally need lat/lng, view_km, chat_radius_km, polygon,
 * and review.{operation,address,pin,boundary,radius}, each containing
 * verified=true, source_url (HTTP/S), checked_at (YYYY-MM-DD), evidence (text).
 * Active polygon_origin must equal source_geometry (never a generated circle).
 * review.radius additionally records numeric join_tolerance_m/leave_tolerance_m
 * and effective_tolerance_evidence explaining the reviewed effective boundaries.
 * These must match explicit join_tolerance_km/leave_tolerance_km for that room.
 * Evidence declarations require human review; this script cannot verify a venue.
 * --validate-only never connects to a DB. Default is DB-aware dry-run.
 * --apply-local is the only writing mode. No updates, deletes or reactivation.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

function requireValue(bool $ok, string $message): void
{
    if (!$ok) { throw new RuntimeException($message); }
}
function validUrl(mixed $url): bool
{
    return is_string($url) && strlen($url) <= 2048 &&
        !preg_match('/[^\x21-\x7E]/', $url) && filter_var($url, FILTER_VALIDATE_URL) &&
        in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) &&
        parse_url($url, PHP_URL_USER) === null && parse_url($url, PHP_URL_PASS) === null;
}
function numberIn(mixed $value, float $min, float $max): bool
{
    return (is_float($value) || is_int($value)) && is_finite((float)$value) && $value >= $min && $value <= $max;
}
function normalized(string $value): string
{
    if (class_exists('Normalizer')) { $value = Normalizer::normalize($value, Normalizer::FORM_C); }
    $value = str_replace(['ß','ä','ö','ü'], ['ss','ae','oe','ue'], mb_strtolower(trim($value)));
    $value = preg_replace('/str\.(?=\s|$)/u', 'strasse', $value);
    $value = preg_replace('/\bstr(?=\s|$)/u', 'strasse', $value);
    return trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value));
}
function nearbySimilar(array $a, array $b): bool
{
    if (!isset($a['lat'],$a['lng'],$b['lat'],$b['lng'])) { return false; }
    $metres = hypot(($a['lat']-$b['lat'])*111320, ($a['lng']-$b['lng'])*111320*cos(deg2rad((float)$a['lat'])));
    if ($metres > 150) { return false; }
    similar_text(normalized($a['name']), normalized($b['name']), $similarity);
    return $similarity >= 80;
}
function orientation(array $a, array $b, array $c): float
{
    return ($b[0]-$a[0])*($c[1]-$a[1])-($b[1]-$a[1])*($c[0]-$a[0]);
}
function validateReviewedTolerances(array $entry, float $joinKm, float $leaveKm): void
{
    if (!$entry['import_ready']) { return; }
    $review = $entry['review']['radius'];
    requireValue(is_finite($joinKm) && is_finite($leaveKm) &&
        abs($review['join_tolerance_m'] - $joinKm * 1000) < 0.000001 &&
        abs($review['leave_tolerance_m'] - $leaveKm * 1000) < 0.000001,
        'Room GPS tolerances differ from reviewed effective boundaries: '.$entry['key']);
}
function validatePolygon(array $entry): void
{
    $p = $entry['polygon'] ?? null;
    requireValue(is_array($p) && ($p['type'] ?? '') === 'Polygon' &&
        isset($p['coordinates']) && is_array($p['coordinates']) && count($p['coordinates']) === 1,
        'Only a reviewed simple Polygon without holes is supported.');
    $ring = $p['coordinates'][0];
    requireValue(is_array($ring) && count($ring) >= 4 && count($ring) <= 2000, 'Invalid polygon ring size.');
    foreach ($ring as $point) {
        requireValue(is_array($point) && array_is_list($point) && count($point) === 2 &&
            numberIn($point[0], 5, 16) && numberIn($point[1], 47, 56), 'Invalid polygon coordinate.');
    }
    requireValue($ring[0] == $ring[count($ring)-1], 'Polygon ring must be closed.');
    $area = 0.0; $inside = false; $n = count($ring)-1;
    for ($i = 0; $i < $n; $i++) {
        $a = $ring[$i]; $b = $ring[$i+1];
        requireValue($a != $b, 'Repeated adjacent polygon vertex.');
        $area += $a[0]*$b[1]-$b[0]*$a[1];
        if (($a[1] > $entry['lat']) !== ($b[1] > $entry['lat']) &&
            $entry['lng'] < ($b[0]-$a[0])*($entry['lat']-$a[1])/($b[1]-$a[1])+$a[0]) { $inside = !$inside; }
        for ($j = $i+2; $j < $n; $j++) {
            if ($i === 0 && $j === $n-1) { continue; }
            $c = $ring[$j]; $d = $ring[$j+1];
            // Reject crossing and touching non-adjacent edges, including collinear overlap.
            $boxesOverlap = max(min($a[0],$b[0]),min($c[0],$d[0])) <= min(max($a[0],$b[0]),max($c[0],$d[0])) &&
                max(min($a[1],$b[1]),min($c[1],$d[1])) <= min(max($a[1],$b[1]),max($c[1],$d[1]));
            requireValue(!$boxesOverlap || orientation($a,$b,$c)*orientation($a,$b,$d) > 0 ||
                orientation($c,$d,$a)*orientation($c,$d,$b) > 0, 'Self-intersecting polygon.');
        }
    }
    requireValue(abs($area) > 1e-12 && $inside, 'Pin must lie inside a nondegenerate reviewed polygon.');
}

// Pure validation helpers are reusable by the DB-free regression test.
if (defined('LINKUUP_NATIONAL_IMPORT_HELPERS_ONLY') && LINKUUP_NATIONAL_IMPORT_HELPERS_ONLY) { return; }
$apply = false; $validateOnly = false; $input = 'import-reviewed.json';
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--apply-local') { $apply = true; }
    elseif ($arg === '--validate-only') { $validateOnly = true; }
    elseif (str_starts_with($arg, '--input=')) { $input = substr($arg, 8); }
    else { throw new InvalidArgumentException('Unknown argument: '.$arg); }
}
requireValue(!($apply && $validateOnly), 'Cannot combine --apply-local and --validate-only.');
$directory = realpath(dirname(__DIR__).'/data/location-expansion-4');
$path = $directory ? realpath($directory.'/'.$input) : false;
requireValue($path !== false && str_starts_with($path, $directory.DIRECTORY_SEPARATOR), 'Input must exist inside location-expansion-4.');
$doc = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
requireValue(($doc['schema_version'] ?? null) === 1 && isset($doc['entries']) && is_array($doc['entries']) &&
    array_is_list($doc['entries']) && count($doc['entries']) > 0, 'Expected schema_version=1 and nonempty entries list.');
$entries = $doc['entries']; $seen = []; $spatialSeen = []; $held = [];
$counts = ['validated'=>0, 'active'=>0, 'pending'=>0, 'existing'=>0, 'held_duplicates'=>0, 'inserted'=>0];
foreach ($entries as &$e) {
    requireValue(is_array($e), 'Invalid entry.');
    foreach (['key'=>100,'name'=>64,'city'=>128,'street'=>128,'zip'=>5] as $field=>$max) {
        requireValue(isset($e[$field]) && is_string($e[$field]) && trim($e[$field]) !== '' && mb_strlen($e[$field]) <= $max,
            'Invalid field: '.$field);
    }
    requireValue((bool)preg_match('/^(osm-(node|way|relation)-[0-9]+|reviewed-[a-z0-9-]+)$/D', $e['key']), 'Invalid stable source key.');
    requireValue((bool)preg_match('/^[0-9]{5}$/D', $e['zip']), 'Invalid postal code.');
    requireValue(isset($e['category']) && is_int($e['category']) && $e['category'] >= 2 && $e['category'] <= 21, 'Invalid category.');
    requireValue(validUrl($e['source_url'] ?? null) && (!isset($e['website']) || validUrl($e['website'])), 'Invalid source/website URL.');
    requireValue(isset($e['import_ready']) && is_bool($e['import_ready']), 'Explicit import_ready boolean required.');
    foreach ($spatialSeen as $previous) { requireValue(!nearbySimilar($e,$previous), 'Nearby similar input venue needs review: '.$e['key']); }
    $spatialSeen[] = $e;
    foreach (['key:'.$e['key'], 'name:'.normalized($e['city']).':'.normalized($e['name']),
        'address:'.normalized($e['city']).':'.normalized($e['street'])] as $identity) {
        requireValue(!isset($seen[$identity]), 'Duplicate in input: '.$e['key']); $seen[$identity] = true;
    }
    if ($e['import_ready']) {
        foreach (['operation','address','pin','boundary','radius'] as $field) {
            $r = $e['review'][$field] ?? [];
            $date = $r['checked_at'] ?? '';
            $parsed = is_string($date) ? DateTimeImmutable::createFromFormat('!Y-m-d', $date) : false;
            requireValue(($r['verified'] ?? null) === true && validUrl($r['source_url'] ?? null) &&
                is_string($r['evidence'] ?? null) && mb_strlen(trim($r['evidence'])) >= 20 &&
                $parsed && $parsed->format('Y-m-d') === $date && $date <= gmdate('Y-m-d'), 'Missing valid review: '.$field);
        }
        requireValue(numberIn($e['lat'] ?? null, 47, 56) && numberIn($e['lng'] ?? null, 5, 16), 'Invalid German-region pin.');
        requireValue(numberIn($e['view_km'] ?? null, .01, 32) && numberIn($e['chat_radius_km'] ?? null, .001, 10), 'Invalid reviewed radii.');
        $radiusReview = $e['review']['radius'];
        requireValue(numberIn($radiusReview['join_tolerance_m'] ?? null, 0, 10000) &&
            numberIn($radiusReview['leave_tolerance_m'] ?? null, 0, 10000) &&
            is_string($radiusReview['effective_tolerance_evidence'] ?? null) &&
            mb_strlen(trim($radiusReview['effective_tolerance_evidence'])) >= 20,
            'Review must document numeric GPS tolerances and their effective boundary impact.');
        requireValue(numberIn($e['join_tolerance_km'] ?? null, 0, 10) && numberIn($e['leave_tolerance_km'] ?? null, 0, 10) &&
            $e['leave_tolerance_km'] >= $e['join_tolerance_km'], 'Explicit room tolerances required; leave must not be smaller than join.');
        validateReviewedTolerances($e, $e['join_tolerance_km'], $e['leave_tolerance_km']);
        requireValue(($e['polygon_origin'] ?? null) === 'source_geometry', 'A sourced polygon is required, never a generated circle.');
        validatePolygon($e); $counts['active']++;
    } else {
        requireValue(numberIn($e['lat'] ?? null, 47, 56) && numberIn($e['lng'] ?? null, 5, 16), 'Pending entry needs valid source coordinates.');
        // Retain source coordinates only as an explicitly unverified, disabled candidate.
        $e['polygon'] = $e['chat_radius_km'] = $e['view_km'] = null;
        $e['join_tolerance_km'] = $e['leave_tolerance_km'] = null;
        $counts['pending']++;
    }
    $publicSource = preg_match('/^osm-(node|way|relation)-([0-9]+)$/D', $e['key'], $osmParts)
        ? 'https://www.openstreetmap.org/'.$osmParts[1].'/'.$osmParts[2] : $e['source_url'];
    $e['_info'] = 'Quelle: OpenStreetMap contributors (ODbL). ['.$e['key'].'] '.$publicSource;
    if (!$e['import_ready']) { $e['_info'] .= ' | Recherchekandidat deaktiviert; Betrieb, Pin und Chatgrenze nicht freigegeben.'; }
    requireValue(mb_strlen($e['_info']) <= 512, 'Source metadata exceeds room_info limit.');
    $counts['validated']++;
}
unset($e);
if ($validateOnly) {
    echo json_encode(['mode'=>'validate-only','input_sha256'=>hash_file('sha256',$path),'counts'=>$counts], JSON_PRETTY_PRINT).PHP_EOL; exit;
}
$root = dirname(__DIR__, 3);
require $root.'/protected/config.php';
require $root.'/GDO7.php';
requireValue(in_array(GDO_DB_HOST, ['localhost','127.0.0.1','::1'], true), 'Only a local database is allowed.');
$db = \GDO\DB\Database::init()->connect();
// Advisory lock serializes this importer; an unrelated writer still needs operational coordination.
$locked = false; $transaction = false;
try {
    if ($apply) {
        $locked = (int)$db->query("SELECT GET_LOCK('linkuup_national_import', 0) AS acquired")->fetch_assoc()['acquired'] === 1;
        requireValue($locked, 'Another national import is running.');
        requireValue($db->begin_transaction(), 'Could not begin transaction.'); $transaction = true;
    }
    $existingIdentities = [];
    $existingSpatial = [];
    $existingRows = $db->query('SELECT r.room_name,r.room_pos_lat,r.room_pos_lng,a.address_city,a.address_street FROM lup_room r LEFT JOIN gdo_address a ON a.address_id=r.room_address');
    requireValue($existingRows !== false, 'Existing room lookup failed.');
    while ($existing = $existingRows->fetch_assoc()) {
        $city = normalized((string)$existing['address_city']);
        $existingIdentities['name:'.$city.':'.normalized((string)$existing['room_name'])] = true;
        if (!empty($existing['address_street'])) { $existingIdentities['address:'.$city.':'.normalized($existing['address_street'])] = true; }
        $existingSpatial[] = ['name'=>$existing['room_name'],'lat'=>$existing['room_pos_lat'],'lng'=>$existing['room_pos_lng']];
    }
    $find = $db->prepare('SELECT room_id FROM lup_room WHERE room_info LIKE ?');
    $category = $db->prepare('SELECT cat_id FROM lup_category WHERE cat_id=?');
    $address = $db->prepare('INSERT INTO gdo_address (address_name,address_street,address_zip,address_city,address_country) VALUES (?,?,?,?,?)');
    $room = $db->prepare('INSERT INTO lup_room (room_name,room_info,room_color,room_category,room_pos_lat,room_pos_lng,room_polygon,room_view,room_radius,room_www,room_address,room_rating,room_needs_vip,room_has_mira,room_show_distance,room_enabled,room_join_tolerance,room_leave_tolerance) VALUES (?,?,?,?,?,?,?,?,?,?,?,0,0,0,1,?,?,?)');
    foreach ($entries as $e) {
        requireValue($category->execute([$e['category']]), 'Category query failed.');
        requireValue($category->get_result()->num_rows === 1, 'Missing category: '.$e['category']);
        requireValue($find->execute(['%['.$e['key'].']%']), 'Source lookup failed.');
        if ($find->get_result()->num_rows) { $counts['existing']++; continue; }
        $collision = isset($existingIdentities['name:'.normalized($e['city']).':'.normalized($e['name'])]) ||
            isset($existingIdentities['address:'.normalized($e['city']).':'.normalized($e['street'])]);
        foreach ($existingSpatial as $previous) { if (nearbySimilar($e,$previous)) { $collision = true; break; } }
        if ($collision) {
            requireValue(!$e['import_ready'], 'Existing identity needs review: '.$e['key']);
            $held[] = $e['key']; $counts['held_duplicates']++; continue;
        }
        if (!$apply) { continue; }
        requireValue($address->execute([$e['name'],$e['street'],$e['zip'],$e['city'],'DE']), 'Address insert failed.');
        $aid = $db->insert_id;
        $polygon = $e['polygon'] === null ? null : json_encode($e['polygon'], JSON_THROW_ON_ERROR);
        requireValue($room->execute([$e['name'],$e['_info'],'#91BFF3',$e['category'],$e['lat'],$e['lng'],
            $polygon,$e['view_km'],$e['chat_radius_km'],$e['website'] ?? $e['source_url'],$aid,(int)$e['import_ready'],
            $e['join_tolerance_km'],$e['leave_tolerance_km']]), 'Room insert failed.');
        $counts['inserted']++;
    }
    if ($transaction) { requireValue($db->commit(), 'Commit failed.'); $transaction = false; }
} catch (Throwable $error) {
    if ($transaction) { $db->rollback(); }
    throw $error;
} finally {
    if ($locked) { $db->query("SELECT RELEASE_LOCK('linkuup_national_import')"); }
}
echo json_encode(['mode'=>$apply ? 'local-import' : 'dry-run','input_sha256'=>hash_file('sha256',$path),'counts'=>$counts,'held_duplicates'=>$held], JSON_PRETTY_PRINT).PHP_EOL;
