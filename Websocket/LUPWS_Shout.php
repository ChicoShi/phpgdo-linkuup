<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Websocket;

use GDO\DB\Database;
use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUPWS_Command;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/** Send one paid message to every currently occupied Location. */
final class LUPWS_Shout extends LUPWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$user = $msg->user();
		if ($user->isGuest())
		{
			return $msg->rplyError('err_member_only');
		}
		$text = trim($msg->readString());
		if ($text === '' || strlen($text) > 512)
		{
			return $msg->rplyError('err_lup_shout_text');
		}

		$cost = Module_LinkUUp::instance()->cfgShoutCost();
		if (!$this->chargeCredits($user, $cost))
		{
			return $msg->rplyError('err_lup_shout_credits', [$cost, $this->creditBalance($user)]);
		}

		[$locations, $recipients] = LUP_Global::shout($user, $text);
		return $msg->replyBinary($msg->cmd(),
			GWS_Message::wr32($this->creditBalance($user)) .
			GWS_Message::wr32($locations) .
			GWS_Message::wr32($recipients));
	}

	/** Conditional update makes concurrent shouts unable to overspend credits. */
	private function chargeCredits(GDO_User $user, int $cost): bool
	{
		if ($cost <= 0)
		{
			return true;
		}
		$table = GDO_UserSetting::table()->gdoTableIdentifier();
		$userId = (int)$user->getID();
		Database::instance()->queryWrite("UPDATE {$table} SET uset_var=uset_var-{$cost} WHERE uset_user={$userId} AND uset_name='credits' AND uset_var+0>={$cost}");
		return Database::instance()->affectedRows() === 1;
	}

	private function creditBalance(GDO_User $user): int
	{
		$setting = GDO_UserSetting::getById($user->getID(), 'credits');
		return $setting ? (int)$setting->gdoVar('uset_var') : 0;
	}
}

GWS_Commands::register(0x1166, new LUPWS_Shout());
