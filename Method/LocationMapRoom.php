<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Array;
use GDO\Core\GDT_Object;
use GDO\Core\MethodAjax;
use GDO\LinkUUp\LUP_Room;
use GDO\User\GDO_User;

/** Lazily supplies one editable room to the staff map editor. */
final class LocationMapRoom extends MethodAjax
{
	public function gdoParameters(): array
	{
		return [GDT_Object::make('room')->table(LUP_Room::table())->notNull()];
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$room = $this->gdoParameterValue('room');
		if ($room && $room->canEdit($user))
		{
			return true;
		}
		$error = 'err_not_allowed';
		return false;
	}

	public function execute(): GDT
	{
		return GDT_Array::make()->value(LocationMap::mapRoomJSON($this->gdoParameterValue('room')));
	}
}
