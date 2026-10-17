<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Websocket;

use GDO\DB\Database;
use GDO\LinkUUp\LUPWS_Command;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;
use GDO\Util\WS;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/** Buy permanent personal room-entry tolerance in whole metres. */
final class LUPWS_ToleranceBoost extends LUPWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$user = $msg->user();
		if ($user->isGuest())
		{
			return $msg->rplyError('err_member_only');
		}
		$meters = $msg->read16u();
		if ($meters < 1 || $meters > 1000)
		{
			return $msg->rplyError('err_lup_tolerance_meters');
		}
		$module = Module_LinkUUp::instance();
		$oldBoost = (float)$module->userSettingValue($user, 'tolerance_boost');
		$newBoost = $oldBoost + ($meters / 1000.0);
		if ($newBoost > 10.0)
		{
			return $msg->rplyError('err_lup_tolerance_limit');
		}
		// Credits are stored as integers. Charge the exact metred proportion; if
		// an administrator chooses a rate that cannot be divided into whole
		// credits, round the fractional remainder up rather than undercharge.
		$cost = (int)ceil(($meters * $module->cfgToleranceCreditsPerKM()) / 1000);
		if (!$this->chargeCredits($user, $cost))
		{
			return $msg->rplyError('err_lup_tolerance_credits', [$cost, $this->creditBalance($user)]);
		}
		$module->saveUserSetting($user, 'tolerance_boost', (string)$newBoost);
		return $msg->replyBinary($msg->cmd(),
			GWS_Message::wr32($this->creditBalance($user)) . WS::wrFloat($newBoost));
	}

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

GWS_Commands::register(0x1168, new LUPWS_ToleranceBoost());
