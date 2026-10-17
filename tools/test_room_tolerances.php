<?php
declare(strict_types=1);
/** Actual LUP_Room + GDT_Polygon, with config/user stubs; no DB. */
namespace GDO\User { class GDO_User {} }
namespace GDO\LinkUUp {
    final class Module_LinkUUp {
        public static function instance(): self { return new self(); }
        public function userSettingValue($user, $key): float { return 10.0; }
        public function cfgRoomTolerance(): float { return .064; }
        public function cfgRoomLeaveTolerance(): float { return .640; }
    }
}
namespace {
    require dirname(__DIR__,3).'/GDO7.php';
    $room = (new ReflectionClass(\GDO\LinkUUp\LUP_Room::class))->newInstanceWithoutConstructor();
    $polygon = json_encode(['type'=>'Polygon','coordinates'=>[[[13.399,52.499],[13.401,52.499],[13.401,52.501],[13.399,52.501],[13.399,52.499]]]]);
    $vars = ['room_polygon'=>$polygon,'room_join_tolerance'=>'0.003','room_leave_tolerance'=>'0.005'];
    $room->setGDOVars($vars);
    $checks = 0;
    $check = function (bool $ok, string $label) use (&$checks): void {
        if (!$ok) { throw new RuntimeException($label); } $checks++; echo $label." PASS\n";
    };
    $outside = fn(float $metres): float => 52.501 + $metres/111320;
    $check($room->isInChatRange(52.5,13.4),'Interior enters');
    $check($room->isInChatRange($outside(2),13.4),'Two metres outside enters');
    $check(!$room->isInChatRange($outside(4),13.4),'Four metres outside cannot enter');
    $check($room->isInChatLeaveRange($outside(4),13.4),'Four metres outside retained');
    $check(!$room->isInChatLeaveRange($outside(6),13.4),'Six metres outside leaves');
    $user = (new ReflectionClass(\GDO\User\GDO_User::class))->newInstanceWithoutConstructor();
    $check($room->isInChatRange(52.5,13.4,$user), 'Boosted account inside reviewed venue enters');
    $check(!$room->isInChatRange($outside(4),13.4,$user), '10 km boost cannot expand reviewed join');
    $check(!$room->isInChatLeaveRange($outside(6),13.4,$user), '10 km boost cannot expand reviewed leave');
    $check($room->isInChatLeaveRange($outside(4),13.4,$user), 'Boosted account retains reviewed hysteresis');
    $room->setGDOVars(['room_polygon'=>$polygon,'room_join_tolerance'=>'0','room_leave_tolerance'=>'0']);
    $check(!$room->isInChatRange($outside(1),13.4),'Explicit zero does not fall back');
    $room->setGDOVars(['room_polygon'=>$polygon,'room_join_tolerance'=>null,'room_leave_tolerance'=>null]);
    $check($room->getJoinTolerance() === .064 && $room->getLeaveTolerance() === .640,'NULL retains existing globals');
    $check($room->isInChatRange($outside(60),13.4),'Existing room retains 64 m join');
    $check(!$room->isInChatRange($outside(70),13.4),'Existing room rejects beyond join');
    $check($room->isInChatLeaveRange($outside(600),13.4),'Existing room retains 640 m leave');
    $check(!$room->isInChatLeaveRange($outside(650),13.4),'Existing room rejects beyond leave');
    $check($room->isInChatRange($outside(1000),13.4,$user), 'Legacy account boost still applies to join');
    $check($room->isInChatLeaveRange($outside(1000),13.4,$user), 'Legacy account boost still applies to leave');
    $room->setGDOVars(['room_polygon'=>$polygon]);
    $check($room->getJoinTolerance() === .064,'Old cached row missing columns retains globals');
    $field = \GDO\UI\GDT_Length::make('room_join_tolerance')->initial(null);
    $wire = $field->var(null)->renderBinary();
    $check(strlen($wire) === 4 && is_nan(unpack('fvalue',$wire)['value']),'Nullable Length serializes as four-byte NaN');
    $wire = $field->var('0.003')->renderBinary();
    $check(abs(unpack('fvalue',$wire)['value'] - .003) < .000000001,'Per-room Length wire value');
    foreach ([
        'join only'=>['0.003',null], 'leave only'=>[null,'0.005'],
        'negative'=>['-1','0.005'], 'reversed'=>['0.005','0.003'],
        'nonnumeric'=>['oops','0.005'], 'infinite'=>['0.003','1e999'],
        'oversize'=>['0.003','11'],
    ] as $label=>[$join,$leave]) {
        $room->setGDOVars(['room_polygon'=>$polygon,'room_join_tolerance'=>$join,'room_leave_tolerance'=>$leave]);
        $check($room->hasIndividualTolerances() && !$room->hasValidTolerancePair() &&
            !$room->isInChatRange(52.5,13.4,$user) && !$room->isInChatLeaveRange(52.5,13.4,$user),
            'Invalid pair fails closed: '.$label);
    }
    $room->setGDOVars(['room_polygon'=>$polygon,'room_join_tolerance'=>'0','room_leave_tolerance'=>'0']);
    $check($room->hasValidTolerancePair() && $room->isInChatRange(52.5,13.4,$user), 'Valid zero pair enters inside');
    echo "$checks DB-free boundary checks passed.\n";
}
