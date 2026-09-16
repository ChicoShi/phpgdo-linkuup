<?php
namespace GDO\LinkUUp\Websocket;

use GDO\Friends\GDO_Friendship;
use GDO\Friends\Method\Remove;
use GDO\LinkUUp\LUP_Notification;
use GDO\User\GDO_User;
use GDO\Websocket\Server\GWS_Command;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;

/**
 * WebSocket command for friend remove.
 *
 * @author gizmore
 */
class LUPWS_FriendsRemove extends GWS_Command
{

	public function hookFriendsRemove($userid, $friendid)
	{
		$this->sendNotifications($userid, $friendid);
	}

	private function sendNotifications($userid, $friendid)
	{
		$sent = [];

		# To both involved
		$user = GDO_User::getById($userid);
		$this->sendNotification($user, $userid, $friendid);
		$sent[$user->getID()] = true;

		$user = GDO_User::getById($friendid);
		$this->sendNotification($user, $userid, $friendid);
		$sent[$user->getID()] = true;

		# To their friends
		$result = GDO_Friendship::getFriendsQuery(GDO_User::getById($userid))->exec();
		$table = GDO_User::table();
		while ($user = $table->fetch($result))
		{
			if (!isset($sent[$user->getID()]))
			{
				$this->sendNotification($user, $userid, $friendid);
				$sent[$user->getID()] = true;
			}
		}

		$result = GDO_Friendship::getFriendsQuery(GDO_User::getById($friendid))->exec();
		while ($user = $table->fetch($result))
		{
			if (!isset($sent[$user->getID()]))
			{
				$this->sendNotification($user, $userid, $friendid);
			}
		}
	}

	private function sendNotification(GDO_User $user, $userid, $friendid)
	{
		# Insert into notification table
		$notification = LUP_Notification::blank([
			'note_user' => $user->getID(),
			'note_data' => json_encode(['type' => 'nofriends', 'user' => $userid, 'friend' => $friendid]),
		])->insert();
		# Send to friends
		$payload = GWS_Message::wrCmd(0x1141) . $this->gdoToBinary($notification);
		GWS_Global::sendBinary($user, $payload);
	}

	public function execute(GWS_Message $msg)
	{
		$friendid = $msg->read32u();
		$userid = $msg->user()->getID();
		if (!GDO_Friendship::getById($userid, $friendid))
		{
			return $msg->rplyError('err_friend_request');
		}
		// Method parameters come from inputs(), not the process-global HTTP
		// request array. The old call could return an error and still reply success.
		Remove::make()->inputs(['friend' => (string)$friendid])->executeWithInit();
		if (GDO_Friendship::getById($userid, $friendid) ||
			GDO_Friendship::getById($friendid, $userid))
		{
			return $msg->rplyError('err_friend_request');
		}
		$msg->user()->tempUnset('gdo_friendship_count');
		GWS_Global::getOrLoadUserById($friendid)->tempUnset('gdo_friendship_count');
		// The HTTP method emits FriendsRemove. Its hook sends the event once.
		return $msg->replyBinary($msg->cmd());
	}

}

GWS_Commands::register(0x1134, new LUPWS_FriendsRemove());
