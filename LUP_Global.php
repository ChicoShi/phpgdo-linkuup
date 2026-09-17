<?php
namespace GDO\LinkUUp;

use GDO\Avatar\GDO_Avatar;
use GDO\Friends\GDO_FriendRequest;
use GDO\Friends\GDO_Friendship;
use GDO\Friends\GDT_FriendRelation;
use GDO\Maps\Position;
use GDO\Maps\Module_Maps;
use GDO\Net\HTTP;
use GDO\User\GDO_User;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;

final class LUP_Global
{

	public static $ROOMS = [];
	/**
	 * @var GDO_User[]
	 */
	public static $ROOM_USERS = [];
	public static $USER_AVATARS = [];
	public static $USER_STATUS = [];
	public static $USER_TROPHY = [];
	public static $USER_LIKES = [];
	/** @var array<int, string[]> Volatile chat payloads, newest item last. */
	public static $ROOM_MESSAGES = [];
	/** @var array<int, array{room:LUP_Room,last:float,lines:array<int,array<string,string>>}> */
	public static $ROOM_DOG_BACKLOG = [];

	##################
	### Visibility ###
	##################
	/**
	 * Current user positions.
	 *
	 * @var array<int, array<int, array{0:float,1:float,2:float}>>
	 */
	public static $POSITIONS = [];

	public static function userSeesUser(GDO_User $a, GDO_User $b)
	{
		$namesSeeingA = array_keys(self::usersSeeing($a));
		return in_array($b->getID(), $namesSeeingA);
	}

	##############
	### Helper ###
	##############

	public static function usersSeeing(GDO_User $user)
	{
		$back = [];
		foreach (self::$ROOM_USERS as $users)
		{
			if (isset($users[$user->getID()]))
			{
				foreach ($users as $user_id => $_user)
				{
					if (!isset($back[$user_id]))
					{
						$back[$user_id] = $_user;
					}
				}
			}
		}
		return $back;
	}

	public static function userListPayload(LUP_Room $room)
	{
		return
			GWS_Message::payload(0x1105) .
			self::userListPayloadData($room);
	}

	public static function userListPayloadData(LUP_Room $room, $withRoomId = true)
	{
		$payload = '';
		if ($withRoomId)
		{
			$payload = GWS_Message::wr32($room->getID());
		}
		if (isset(self::$ROOM_USERS[$room->getID()]))
		{
			foreach (self::$ROOM_USERS[$room->getID()] as $user)
			{
				$payload .= GWS_Message::wr32($user->getID());
			}
		}
		return $payload;
	}

	public static function fullUserPayload(GDO_User $user)
	{
		return
			GWS_Message::wr32($user->getID()) .
			GWS_Message::wr16($user->gdoColumn('user_type')->value($user->getType())->enumIndex()) .
			GWS_Message::wr32($user->getLevel()) .
			GWS_Message::wrS($user->getName()) .
			GWS_Message::wrS($user->getGuestName()) .
//			GWS_Message::wr16('') . # real name
			GWS_Message::wr32(self::avatarFileForUser($user)) .
//			GWS_Message::wr16(self::avatarVersionForUser($user)) .
			GWS_Message::wr16(self::genderPayload($user)) .
			GWS_Message::wr16(self::sexualOrientationPayload($user)) .
			GWS_Message::wr16(self::sexualInterestPayload($user)) .
			GWS_Message::wr16(self::friendshipStatusPayload($user)) .
			GWS_Message::wr8(self::friendshipPendingPayload($user)) .
			GWS_Message::wr8(self::friendshipIncomingPayload($user)) .
			GWS_Message::wrS(self::countryPayload($user)) .
			self::trophyDataForUser($user) .
			GWS_Message::wr32($user->getCredits()) .
			GWS_Message::wrS(self::profileRolePayload($user));
	}

	/** A compact public role label for the app profile header. */
	public static function profileRolePayload(GDO_User $user): string
	{
		if ($user->isAdmin())
		{
			return 'PROFILE_ROLE_ADMIN';
		}
		if ($user->isStaff())
		{
			return 'PROFILE_ROLE_STAFF';
		}
		$userId = (int)$user->getID();
		if (LUP_Room::table()->select('room_id')->where("room_owner={$userId}")->first()->exec()->fetchVar())
		{
			return 'PROFILE_ROLE_BOSS';
		}
		if (LUP_Workers::table()->select('work_room')->where("work_user={$userId}")->first()->exec()->fetchVar())
		{
			return 'PROFILE_ROLE_CREW';
		}
		if (self::isVIP($user))
		{
			return 'PROFILE_ROLE_VIP';
		}
		return $user->isGuest() ? 'PROFILE_ROLE_GUEST' : 'PROFILE_ROLE_MEMBER';
	}

    public static function trophyDataForUser(GDO_User $user)
    {
        $trophy = self::trophyForUser($user);
        return
            GWS_Message::wr32(GDO_Friendship::count($user)) .
            GWS_Message::wrS($trophy->getStatus()) .
            GWS_Message::wr8($trophy->isVIP() ? 1 : 0) .
            GWS_Message::wr32($trophy->getLikeCount()) .
            GWS_Message::wr32($trophy->getChatSent()) .
            GWS_Message::wr32($trophy->getQuerySent()) .
            GWS_Message::wr32($trophy->getQueryRecieved()) .
            GWS_Message::wr32($trophy->getVisits()).
            GWS_Message::wr32(Module_LinkUUp::instance()->cfgCuddles($user));
    }

    public static function avatarFileForUser(GDO_User $user)
	{
		return self::avatarForUser($user)->getFileID();
	}

// 	private static function birthdayPayload(GDO_User $user)
// 	{
// 	    $bday = Module_Birthday::instance()->userSettingValue($user, 'birthday');
// 	    return $bday;
// 	}

	/**
	 *
	 * @param GDO_User $user
	 *
	 * @return GDO_Avatar
	 */
	public static function avatarForUser(GDO_User $user)
	{
		return GDO_Avatar::forUser($user);
	}

	private static function genderPayload(GDO_User $user)
	{
		return $user->setting('User', 'gender')->enumIndex();
	}

	private static function sexualOrientationPayload(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return 0;
		}
		return self::sexualOrientationFor($user)->enumIndex();
	}

	/**
	 * @param GDO_User $user
	 *
	 * @return GDT_SexualOrientation
	 */
	private static function sexualOrientationFor(GDO_User $user)
	{
		return Module_LinkUUp::instance()->userSetting($user, 'lup_sexo');
	}

	private static function sexualInterestPayload(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return 0;
		}
		return self::sexualInterestFor($user)->enumIndex();
	}

	/**
	 * @param GDO_User $user
	 *
	 * @return GDT_RelationInterest
	 */
	private static function sexualInterestFor(GDO_User $user)
	{
		return Module_LinkUUp::instance()->userSetting($user, 'lup_interest');
	}

	####################
	### Trophy Cache ###
	####################

	private static function friendshipStatusPayload(GDO_User $user)
	{
		$enumValue = GDO_Friendship::getRelationBetween(GDO_User::current(), $user);
		return GDT_FriendRelation::make()->enumIndexFor($enumValue);
	}

	private static function friendshipPendingPayload(GDO_User $user)
	{
		$pending = GDO_FriendRequest::table()->getPendingFor(GDO_User::current(), $user);
		return $pending && !$pending->isDenied() ? 1 : 0;
	}

	private static function friendshipIncomingPayload(GDO_User $user)
	{
		$pending = GDO_FriendRequest::table()->getPendingFor($user, GDO_User::current());
		return $pending && !$pending->isDenied() ? 1 : 0;
	}

	private static function countryPayload(GDO_User $user)
	{
		return $user->settingVar('Country', 'country_of_origin');
	}


	####################
	### Avatar Cache ###
	####################

	/**
	 * @param GDO_User $user
	 *
	 * @return LUP_Trophy
	 */
	public static function trophyForUser(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return LUP_Trophy::blank(['lt_uid' => $user->getID()]);
		}
		return LUP_Trophy::getOrCreate($user);
	}

	public static function isVIP(GDO_User $user)
	{
		return self::trophyForUser($user)->isVIP();
	}

	#############
	### Rooms ###
	#############

	public static function processAvatarChange(GDO_User $user, array $row)
	{
		self::$USER_AVATARS[$user->getID()] = [
			'avatar_mode' => $row['avatar_mode'],
			'avatar_file' => $row['avatar_file'],
			'avatar_version' => $row['avatar_version'],
		];
	}

	/**
	 * @param int $id
	 *
	 * @return LUP_Room
	 */
	public static function getRoom($id)
	{
		if (!isset(self::$ROOMS[$id]))
		{
			// The long-running websocket can retain a stale GDO table cache after
			// installer changes. Fetch the room once from the database instead.
			$room = LUP_Room::table()->select()
				->where('lup_room.room_id=' . LUP_Room::quoteS((string)$id))
				->first()->exec()->fetchObject();
			if (!$room)
			{
				return false;
			}
			self::$ROOMS[$id] = $room;
			self::$ROOM_USERS[$id] = [];
		}
		return self::$ROOMS[$id];
	}

	/**
	 * Reload the mutable room fields while keeping the live visitor map intact.
	 * The websocket process is long-running, so vote totals would otherwise
	 * remain frozen at the value from the first room lookup.
	 */
	public static function refreshRoom($id)
	{
		$room = LUP_Room::table()->select()
			->where('lup_room.room_id=' . LUP_Room::quoteS((string)$id))
			->first()->exec()->fetchObject();
		if (!$room)
		{
			return false;
		}
		self::$ROOMS[$id] = $room;
		self::$ROOM_USERS[$id] ??= [];
		return $room;
	}

	public static function isUserInRoom(GDO_User $user, LUP_Room $room)
	{
		return isset(self::$ROOM_USERS[$room->getID()][$user->getID()]);
	}

	public static function userQuit(GDO_User $user)
	{
		foreach (self::getRoomsForUser($user) as $room)
		{
			self::part($room, $user);
		}
	}

	/**
	 * @param GDO_User $user
	 *
	 * @return LUP_Room[]
	 */
	public static function getRoomsForUser(GDO_User $user)
	{
		$userid = $user->getID();
		$rooms = [];
		foreach (self::$ROOM_USERS as $roomId => $users)
		{
			if (isset($users[$userid]))
			{
				$rooms[] = self::$ROOMS[$roomId];
			}
		}
		return $rooms;
	}

	public static function part(LUP_Room $room, GDO_User $user)
	{
		LUP_RoomVisit::onPart($room, $user);

		$payload = GWS_Message::payload(0x1104);
		$payload .= GWS_Message::wrTS();
		$payload .= GWS_Message::wr32($room->getID());
		$payload .= GWS_Message::wr32($user->getID());
		GWS_Global::broadcastBinary($payload);
		unset(self::$ROOM_USERS[$room->getID()][$user->getID()]);
	}

	public static function join(LUP_Room $room, GDO_User $user)
	{
		$id = $room->getID();
		if (!isset(self::$ROOM_USERS[$id][$user->getID()]))
		{
			self::$ROOM_USERS[$id][$user->getID()] = $user;

			$payload = GWS_Message::payload(0x1103);
			$payload .= GWS_Message::wrTS();
			$payload .= GWS_Message::wr32($room->getID());
			$payload .= GWS_Message::wr32($user->getID());
			# Announce to all
			GWS_Global::broadcastBinary($payload);
		}
	}

	/** Send the volatile room backlog before broadcasting the new join. */
	public static function replayMessages(LUP_Room $room, GDO_User $user): void
	{
		foreach (self::$ROOM_MESSAGES[$room->getID()] ?? [] as $payload)
		{
			GWS_Global::sendBinary($user, GWS_Message::payload(0x1107) . $payload);
		}
	}

	/** Retain only a small, non-persistent room backlog for newly joining users. */
	public static function rememberMessage(LUP_Room $room, string $payload): void
	{
		$id = $room->getID();
		$size = Module_LinkUUp::instance()->cfgMessageBufferSize();
		if ($size <= 0)
		{
			unset(self::$ROOM_MESSAGES[$id]);
			return;
		}
		self::$ROOM_MESSAGES[$id][] = $payload;
		self::$ROOM_MESSAGES[$id] = array_slice(self::$ROOM_MESSAGES[$id], -$size);
	}

	###########
	### GPS ###
	###########

	public static function chat(LUP_Room $room, GDO_User $user, GWS_Message $message)
	{
		$text = $message->readString();
		$payload = GWS_Message::wr32(time());
		$payload .= GWS_Message::wr32($user->getID());
		$payload .= GWS_Message::wr32($room->getID());
		$payload .= GWS_Message::wrS($text);
		self::rememberDogBacklog($room, $user, $text);
		self::broadcastChatPayload($room, $user, $payload);

		# Payload1 goes sync back
		$message->replyBinary($message->cmd(), $payload);
	}

	/** Deliver a connector-originated chat line; no sender socket needs a sync reply. */
	public static function chatText(LUP_Room $room, GDO_User $user, string $text): void
	{
		$payload = GWS_Message::wr32(time());
		$payload .= GWS_Message::wr32($user->getID());
		$payload .= GWS_Message::wr32($room->getID());
		$payload .= GWS_Message::wrS($text);
		self::broadcastChatPayload($room, $user, $payload, true);
	}

	/** Make a connector minion known to every app client before it speaks. */
	public static function sendMinion(LUP_Room $room, GDO_User $user): void
	{
		$payload = GWS_Message::payload(0x1106) . self::fullUserPayload($user);
		foreach (self::$ROOM_USERS[$room->getID()] ?? [] as $recipient)
		{
			GWS_Global::sendBinary($recipient, $payload);
		}
	}

	/** Keep a short, volatile transcript until the room has gone quiet. */
	public static function rememberDogBacklog(LUP_Room $room, GDO_User $user, string $text): void
	{
		$module = Module_LinkUUp::instance();
		$size = $module->cfgDogBacklog();
		$id = (int)$room->getID();
		if ($size <= 0)
		{
			unset(self::$ROOM_DOG_BACKLOG[$id]);
			return;
		}
		$backlog = self::$ROOM_DOG_BACKLOG[$id] ?? ['room' => $room, 'last' => 0.0, 'lines' => []];
		$now = microtime(true);
		$backlog['last'] = $now;
		$backlog['lines'][] = [
			'time' => sprintf('%s.%06d', date('Y-m-d H:i:s', (int)$now), (int)(($now - floor($now)) * 1000000)),
			'name' => $user->getName(),
			'message' => $text,
		];
		$backlog['lines'] = array_slice($backlog['lines'], -$size);
		self::$ROOM_DOG_BACKLOG[$id] = $backlog;
		error_log(sprintf('[LUP Dog] queued room=%d lines=%d', $id, count($backlog['lines'])));
	}

	/** Flush quiet room transcripts to Dog. A failed delivery remains buffered. */
	public static function flushDogBacklogs(): void
	{
		$module = Module_LinkUUp::instance();
		$url = $module->cfgDogBacklogURL();
		$secret = $module->cfgConnectorSecret();
		if (!$url || !$secret)
		{
			return;
		}
		$now = microtime(true);
		foreach (self::$ROOM_DOG_BACKLOG as $id => $backlog)
		{
			if (($now - $backlog['last']) < $module->cfgDogChill())
			{
				continue;
			}
			$error = '';
			$ok = self::postToDog($url, [
				'secret' => $secret,
				'room' => $id,
				'room_name' => $backlog['room']->getName(),
				'lang' => \GDO\Language\Trans::$ISO,
				'backlog' => json_encode($backlog['lines'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			], $error);
			error_log(sprintf('[LUP Dog] flush room=%d lines=%d result=%s%s', $id, count($backlog['lines']), $ok ? 'ok' : 'failed', $error ? " error={$error}" : ''));
			if ($ok)
			{
				unset(self::$ROOM_DOG_BACKLOG[$id]);
			}
			else
			{
				self::$ROOM_DOG_BACKLOG[$id]['last'] = $now;
			}
		}
	}

	/** @param array<string,mixed> $payload */
	private static function postToDog(string $url, array $payload, string &$error = ''): bool
	{
		return HTTP::post($url, $payload, false, false, false, $error) !== false;
	}

	private static function broadcastChatPayload(LUP_Room $room, GDO_User $user, string $payload, bool $includeSender = false): void
	{
		self::rememberMessage($room, $payload);

		$payload2 = GWS_Message::payload(0x1107);
		$payload2 .= $payload;
		foreach (self::$ROOM_USERS[$room->getID()] as $_user)
		{
			if ($includeSender || $user !== $_user)
			{
				GWS_Global::sendBinary($_user, $payload2);
			}
		}
	}

	/**
	 * Deliver a paid broadcast to occupied locations within the purchased radius.
	 * A shout is deliberately not a normal chat event: recipients must not see
	 * its sender as having joined their room.
	 *
	 * @return array{0:int,1:int} Number of reached locations and recipients.
	 */
	public static function shout(GDO_User $user, string $text, int $radius): array
	{
		[$lat, $lng] = self::lastPositionFor($user);
		$locations = 0;
		$recipients = 0;
		foreach (self::$ROOM_USERS as $roomId => $users)
		{
			if (!$users)
			{
				continue;
			}
			if (!$room = LUP_Room::getById($roomId))
			{
				continue;
			}
			if (Position::distanceCalculation($lat, $lng, $room->getLat(), $room->getLng()) > $radius)
			{
				continue;
			}
			$payload = GWS_Message::payload(0x1167);
			$payload .= GWS_Message::wr32(time());
			$payload .= GWS_Message::wr32($user->getID());
			$payload .= GWS_Message::wr32($roomId);
			$payload .= GWS_Message::wrS($text);
			$locations++;
			foreach ($users as $recipient)
			{
				if (GWS_Global::sendBinary($recipient, $payload))
				{
					$recipients++;
				}
			}
		}
		return [$locations, $recipients];
	}

	/** Calculate velocity from the most recent four live GPS fixes. */
	public static function velocityFor(GDO_User $user, float $lat, float $lng, ?float $now = null): float
	{
		$points = self::$POSITIONS[$user->getID()] ?? [];
		$points[] = [$lat, $lng, $now ?? microtime(true)];
		$points = array_slice($points, -4);
		if (count($points) < 2)
		{
			return 0.0;
		}
		$seconds = $points[array_key_last($points)][2] - $points[0][2];
		if ($seconds <= 0.0)
		{
			return 0.0;
		}
		$distance = 0.0;
		for ($i = 1, $n = count($points); $i < $n; $i++)
		{
			$distance += Position::distanceCalculation($points[$i - 1][0], $points[$i - 1][1], $points[$i][0], $points[$i][1]);
		}
		return $distance / $seconds * 3600.0;
	}

	/** @return array{0:float,1:float}|null Last GPS point received over this WebSocket. */
	public static function lastPositionFor(GDO_User $user): ?array
	{
		$points = self::$POSITIONS[$user->getID()] ?? [];
		if (!$points)
		{
			return null;
		}
		$point = $points[array_key_last($points)];
		return [$point[0], $point[1]];
	}

	/** Start a fresh live GPS sample window after a WebSocket reconnect. */
	public static function resetGPS(GDO_User $user): void
	{
		unset(self::$POSITIONS[$user->getID()]);
	}

	public static function updateGPS(GDO_User $user, float $lat, float $lng, ?float $now = null): void
	{
		$velocity = self::velocityFor($user, $lat, $lng, $now);
		Module_Maps::instance()->recordVelocity($user, $velocity);
		$points = self::$POSITIONS[$user->getID()] ?? [];
		$points[] = [$lat, $lng, $now ?? microtime(true)];
		self::$POSITIONS[$user->getID()] = array_slice($points, -4);
	}

}
