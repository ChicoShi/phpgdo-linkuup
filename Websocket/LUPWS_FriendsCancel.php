<?php
namespace GDO\LinkUUp\Websocket;

use GDO\Friends\GDO_FriendRequest;
use GDO\Websocket\Server\GWS_Command;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/** Revoke an unanswered friend request sent by the current user. */
final class LUPWS_FriendsCancel extends GWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$friendId = $msg->read32u();
		$request = GDO_FriendRequest::getById($msg->user()->getID(), $friendId);
		if ($request && !$request->isDenied())
		{
			$request->delete();
		}
		return $msg->replyBinary($msg->cmd());
	}
}

GWS_Commands::register(0x1136, new LUPWS_FriendsCancel());
