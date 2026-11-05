<?php
declare(strict_types=1);
/** Isolated command regression tests; no database, socket or user writes. */
namespace GDO\User {
    class GDO_User {
        public function getID() { return 7; }
        public function isGhost() { return false; }
    }
}
namespace GDO\LinkUUp {
    class LUPWS_Command { protected function cfgTicketEngine() { return false; } }
    class LUP_Room {
        public $tolerance = '0';
        public function getID() { return 9; }
        public function hasIndividualTolerances() { return $this->tolerance !== null; }
        public function isDisabled() { return false; }
        public function gdoVar($key) { return $this->tolerance; }
        public function isInChatRange($lat, $lng) { return $lat >= 50 && $lat <= 50.001 && $lng >= 8 && $lng <= 8.001; }
        public function isInChatLeaveRange($lat, $lng) { return $lat >= 49.999 && $lat <= 50.002 && $lng >= 7.999 && $lng <= 8.002; }
    }
    class LUP_Global {
        public static $ROOM_USERS = [9 => [7 => true]];
        public static $POSITIONS = [];
        public static $room;
        public static $sent = 0;
        public static function getRoom($id) { return self::$room; }
        public static function isUserInRoom($user, $room) { return true; }
        public static function userListPayload($room) { return 'users'; }
        public static function updateGPS($user, $lat, $lng) { self::$POSITIONS[7][] = [$lat, $lng, microtime(true)]; }
        public static function trophyForUser($user) { return null; }
        public static function chat($room, $user, $msg) { self::$sent++; }
    }
    class LUP_MessageSent { public static function messageSent($room) {} }
}
namespace GDO\Websocket\Server {
    class GWS_Commands { public static function register($code, $command) {} }
    class GWS_Global {
        public static $sent = 0;
        public static function sendBinary($user, $payload) { self::$sent++; }
    }
    class GWS_Message {
        public $error;
        public $coordinates = [50.0005, 8.0005];
        public function user() { return new \GDO\User\GDO_User(); }
        public function read32() { return 9; }
        public function readFloat() { return array_shift($this->coordinates); }
        public function readString() { return ''; }
        public function rplyError($key) { $this->error = $key; }
    }
}
namespace {
    use GDO\LinkUUp\LUP_Global;
    use GDO\LinkUUp\LUP_Room;
    use GDO\LinkUUp\Websocket\LUPWS_Join;
    use GDO\LinkUUp\Websocket\LUPWS_Message;
    use GDO\Websocket\Server\GWS_Global;
    use GDO\Websocket\Server\GWS_Message;
    require dirname(__DIR__).'/Websocket/LUPWS_Join.php';
    require dirname(__DIR__).'/Websocket/LUPWS_Message.php';
    LUP_Global::$room = new LUP_Room();
    $count = 0;
    function check($ok, $label) {
        global $count;
        if (!$ok) throw new \RuntimeException($label);
        $count++;
        echo $label." PASS\n";
    }
    foreach ([
        'missing' => [null, false],
        'stale' => [[50.0005, 8.0005, microtime(true)-121], false],
        'outside' => [[50.01, 8, microtime(true)], false],
        'fresh inside' => [[50.0005, 8.0005, microtime(true)], true],
        'leave hysteresis' => [[50.0015, 8.0005, microtime(true)], true],
        'nonfinite coordinates' => [[NAN, 8, microtime(true)], false],
        'future timestamp' => [[50.0005, 8.0005, microtime(true)+60], false],
    ] as $label => [$fix, $allowed]) {
        LUP_Global::$POSITIONS = $fix ? [7 => [$fix]] : [];
        LUP_Global::$sent = 0;
        $msg = new GWS_Message(); (new LUPWS_Message())->execute($msg);
        check((LUP_Global::$sent === 1) === $allowed && ($allowed || $msg->error === 'err_room_not_near'), 'Message '.$label);
    }
    foreach (['inside'=>[[50.0005,8.0005],true], 'outside'=>[[50.01,8],false], 'invalid'=>[[NAN,8],false]] as $label=>[$coords,$allowed]) {
        GWS_Global::$sent = 0; LUP_Global::$POSITIONS = [];
        $msg = new GWS_Message(); $msg->coordinates=$coords;
        (new LUPWS_Join())->execute($msg);
        check((GWS_Global::$sent === 1) === $allowed && ($allowed || $msg->error === 'err_room_not_near'), 'Repeated join '.$label);
        if ($allowed) check(isset(LUP_Global::$POSITIONS[7][0][2]), 'Repeated join refreshes timestamp');
    }
    LUP_Global::$room->tolerance = null; LUP_Global::$POSITIONS = []; LUP_Global::$sent = 0;
    (new LUPWS_Message())->execute(new GWS_Message());
    check(LUP_Global::$sent === 1, 'Legacy message unchanged without GPS');
    GWS_Global::$sent = 0; $msg = new GWS_Message(); $msg->coordinates=[50.01,8];
    (new LUPWS_Join())->execute($msg);
    check(GWS_Global::$sent === 1 && LUP_Global::$POSITIONS === [], 'Legacy repeated join unchanged');
    echo "$count checks passed.\n";
}
