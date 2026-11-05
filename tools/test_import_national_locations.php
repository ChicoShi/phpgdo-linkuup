<?php
declare(strict_types=1);
/** DB-free regression checks. Run: php tools/test_import_national_locations.php */
define('LINKUUP_NATIONAL_IMPORT_HELPERS_ONLY', true);
require __DIR__.'/import_national_locations.php';
$tested = 0;
function fixtureCheck(array $entries, bool $success, string $label): void
{
    global $tested;
    $file = tempnam(dirname(__DIR__).'/data/location-expansion-4', '.import-test-');
    try {
        file_put_contents($file, json_encode(['schema_version'=>1,'entries'=>$entries], JSON_THROW_ON_ERROR));
        $process = proc_open([PHP_BINARY,__DIR__.'/import_national_locations.php','--validate-only','--input='.basename($file)],
            [1=>['pipe','w'],2=>['pipe','w']], $pipes);
        $stdout = stream_get_contents($pipes[1]); $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]); $code = proc_close($process);
        requireValue(($code === 0) === $success, 'Failed '.$label.': '.$stderr);
        if ($success) {
            $result = json_decode($stdout, true, 512, JSON_THROW_ON_ERROR);
            requireValue($result['mode'] === 'validate-only' && $result['input_sha256'] === hash_file('sha256',$file), 'Wrong validation output.');
        }
        $tested++; echo $label." PASS\n";
    } finally { unlink($file); }
}
$pending = ['key'=>'reviewed-test','name'=>'Test venue','city'=>'Berlin','street'=>'Teststraße 1','zip'=>'10115',
    'category'=>3,'source_url'=>'https://example.com/venue','import_ready'=>false,'lat'=>52.5,'lng'=>13.4];
$active = $pending;
$active += ['view_km'=>32,'chat_radius_km'=>.01,'join_tolerance_km'=>.003,'leave_tolerance_km'=>.005,'polygon_origin'=>'source_geometry',
    'polygon'=>['type'=>'Polygon','coordinates'=>[[[13.399,52.499],[13.401,52.499],[13.401,52.501],[13.399,52.501],[13.399,52.499]]]]];
$active['import_ready'] = true;
foreach (['operation','address','pin','boundary','radius'] as $kind) {
    $active['review'][$kind] = ['verified'=>true,'source_url'=>'https://example.com/evidence','checked_at'=>gmdate('Y-m-d'),
        'evidence'=>'Synthetic regression fixture, never an actual venue approval.'];
}
$active['review']['radius'] += ['join_tolerance_m'=>3,'leave_tolerance_m'=>5,
    'effective_tolerance_evidence'=>'Synthetic evidence explicitly covering the 3 m join and 5 m leave buffers.'];
fixtureCheck([$pending],true,'Pending candidate');
fixtureCheck([$active],true,'Complete active evidence');
$bad=$active; unset($bad['review']['operation']['evidence']); fixtureCheck([$bad],false,'Missing source evidence');
$bad=$active; unset($bad['review']['radius']['join_tolerance_m']); fixtureCheck([$bad],false,'Missing join tolerance');
$bad=$active; $bad['review']['radius']['leave_tolerance_m']='640'; fixtureCheck([$bad],false,'Nonnumeric leave tolerance');
$bad=$active; unset($bad['review']['radius']['effective_tolerance_evidence']); fixtureCheck([$bad],false,'Missing effective-boundary evidence');
$bad=$active; $bad['chat_radius_km']=10.01; fixtureCheck([$bad],false,'Oversized venue radius');
$bad=$active; $bad['view_km']=32.01; fixtureCheck([$bad],false,'Oversized visibility');
$bad=$active; $bad['polygon_origin']='generated_circle'; fixtureCheck([$bad],false,'Generated circle');
$bad=$active; $bad['polygon']['coordinates'][]=$bad['polygon']['coordinates'][0]; fixtureCheck([$bad],false,'Unsupported holes');
$bad=$active; $bad['lat']=53; fixtureCheck([$bad],false,'Pin outside polygon');
$bad=$active; $bad['polygon']['coordinates'][0]=[[13.399,52.499],[13.401,52.501],[13.399,52.501],[13.401,52.499],[13.399,52.499]];
fixtureCheck([$bad],false,'Self-intersecting polygon');
$other=$pending; $other['key']='reviewed-other'; $other['name']='Other venue'; $other['street']='Teststr. 1';
fixtureCheck([$pending,$other],false,'Normalized street duplicate');
$other['street']='Different 2'; $other['name']='Test venues'; fixtureCheck([$pending,$other],false,'Nearby similar venue');
validateReviewedTolerances($active,.003,.005); $tested++; echo "Matching room tolerances PASS\n";
foreach ([[.004,.005],[.003,.006]] as [$join,$leave]) {
    $rejected=false;
    try { validateReviewedTolerances($active,$join,$leave); } catch (RuntimeException) { $rejected=true; }
    requireValue($rejected,'Mismatched room tolerance was accepted.'); $tested++;
}
echo "Mismatched join/leave rejection PASS\n{$tested} DB-free checks passed.\n";
