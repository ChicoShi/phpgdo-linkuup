<?php
namespace GDO\LinkUUp\Websocket;

use GDO\Friends\GDO_FriendRequest;
use GDO\Date\Time;
use GDO\LinkUUp\LUP_Global;
use GDO\User\GDO_User;
use GDO\Websocket\Server\GWS_Command;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/** Deny an incoming friend request from a profile action menu. */
final class LUPWS_FriendsDeny extends GWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$senderId = $msg->read32u();
		$request = GDO_FriendRequest::getById($senderId, $msg->user()->getID());
		if (!$request || $request->isDenied()) return $msg->rplyError('err_friend_request');
		// A redirect from the HTTP handler is not a WebSocket response.
		// Persist the recipient's denial and reply with the updated relation.
		$request->saveVar('frq_denied', Time::getDate());
		$msg->user()->tempUnset('gdo_friendrequest_count');
		$sender = GDO_User::getById($senderId);
		return $msg->replyBinary($msg->cmd(), LUP_Global::fullUserPayload($sender));
	}
}

GWS_Commands::register(0x1137, new LUPWS_FriendsDeny());
