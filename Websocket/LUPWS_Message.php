<?php
namespace GDO\LinkUUp\Websocket;

use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUP_MessageSent;
use GDO\LinkUUp\LUPWS_Command;
use GDO\User\GDO_User;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

class LUPWS_Message extends LUPWS_Command
{
	/** Deliver a trusted Dog response inside the WebSocket process. */
	public function hookLUPDogMessage(int $roomId, int $minionId, string $text): void
	{
		if (($room = LUP_Global::getRoom($roomId)) && ($minion = GDO_User::getById((string)$minionId)))
		{
			LUP_MessageSent::messageSent($room);
			LUP_Global::sendMinion($room, $minion);
			LUP_Global::chatText($room, $minion, $text);
		}
	}

	public function execute(GWS_Message $msg)
	{
		$user = $msg->user();
		if (!($room = LUP_Global::getRoom($msg->read32())))
		{
			return $msg->rplyError('err_room');
		}

		if ($room->isDisabled())
		{
			return $msg->rplyError('err_room_disabled');
		}

		if (!isset(LUP_Global::$ROOM_USERS[$room->getID()][$user->getID()]))
		{
			return $msg->rplyError('err_not_in_room');
		}

		// Only reviewed venues opt into a fresh-position requirement. Use the
		// volatile server receipt timestamp, never the persisted signup location.
		if ($room->gdoVar('room_join_tolerance') !== null)
		{
			$positions = LUP_Global::$POSITIONS[$user->getID()] ?? [];
			$fix = $positions ? $positions[array_key_last($positions)] : null;
			$age = $fix ? microtime(true) - $fix[2] : INF;
			if (!$fix || !is_finite($age) || $age < 0 || $age > 120 ||
				!is_finite($fix[0]) || !is_finite($fix[1]) ||
				abs($fix[0]) > 90 || abs($fix[1]) > 180 ||
				!$room->isInChatLeaveRange($fix[0], $fix[1], $user))
			{
				return $msg->rplyError('err_room_not_near');
			}
		}

		if ($this->cfgTicketEngine())
		{
			if (!$room->getCurrentTicket())
			{
				return $msg->rplyError('err_ticket');
			}
		}

		# Trophy statistics
		if ($trophy = LUP_Global::trophyForUser($user))
		{
			$trophy->countUp('lt_chat_sent');
		}

		# Owner statistics
		LUP_MessageSent::messageSent($room);

		# Send chat messages
		LUP_Global::chat($room, $user, $msg);
	}

}

GWS_Commands::register(0x1107, new LUPWS_Message());
