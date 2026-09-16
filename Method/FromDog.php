<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUP_MessageSent;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;

/** Receive one signed Dog/Mira response and broadcast it into a LinkUUp room. */
final class FromDog extends Method
{
	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('room')->notNull()->table(LUP_Room::table()),
			GDT_String::make('message')->notNull()->max(4096),
		];
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$actual = $_SERVER['HTTP_X_LUP_SECRET'] ?? '';
		if (!Module_LinkUUp::instance()->isSecretCorrect($actual))
		{
			$error = 'err_permission_required';
			return false;
		}
		return true;
	}

	public function execute(): GDT
	{
		$room = $this->gdoParameterValue('room');
		$mira = GDO_User::getByName('mira');
		LUP_Global::sendMinion($room, $mira);
		LUP_MessageSent::messageSent($room);
		LUP_Global::chatText($room, $mira, $this->gdoParameterVar('message'));
		return $this->empty();
	}
}
