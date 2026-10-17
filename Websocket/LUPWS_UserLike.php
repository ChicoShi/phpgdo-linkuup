<?php
namespace GDO\LinkUUp\Websocket;

use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUP_ProfileLike;
use GDO\LinkUUp\LUPWS_Command;
use GDO\Votes\Method\Like;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;

class LUPWS_UserLike extends LUPWS_Command
{

	public function execute(GWS_Message $msg)
	{
		$user = $msg->user();
		$userid = $msg->read32();
		if (!($likeUser = GWS_Global::getOrLoadUserById($userid)))
		{
			return $msg->rplyError('err_user');
		}

		if ($user->getID() === $likeUser->getID())
		{
			return $msg->rplyError('err_like_self');
		}

		if (!LUP_Global::userSeesUser($user, $likeUser))
		{
			return $msg->rplyError('err_user_not_near');
		}

		// Calling execute() directly skips Method::applyInput(), leaving the
		// required `gdo` parameter as an empty GDT_String. Bind the three
		// concrete values through the normal method input path instead.
		$response = Like::make()->execWithInputs([
			'gdo' => LUP_ProfileLike::table()->gdoClassName(),
			'id' => $userid,
			'direction' => 'up',
		]);

		if ($response->hasError())
		{
			return $msg->replyErrorMessage($msg->cmd(), json_encode($response->renderJSON()));
		}
		# reply ok
		$msg->replyBinary($msg->cmd());

// 		# Send async user
// 		$payload = GWS_Message::payload(LUPWS_Command::USER).LUP_Global::fullUserPayload($likeUser);
// 		GWS_Global::sendBinary($user, $payload);
	}

}

GWS_Commands::register(0x1130, new LUPWS_UserLike());
