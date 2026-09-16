<?php
namespace GDO\LinkUUp\Method;

use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\LinkUUp\GDT_RoomEditID;
use GDO\LinkUUp\LUP_Room;
use GDO\QRCode\GDT_QRCode;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_EditButton;
use GDO\User\GDO_User;

/**
 * Room overview for staff, owners and coworkers.
 *
 * @author gizmore
 */
final class Rooms extends MethodQueryTable
{

	##################
	### QueryTable ###
	##################
	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		if ($user->isStaff() || LUP_Room::getEditableRooms($user))
		{
			return true;
		}
		$error = 'err_not_allowed';
		return false;
	}

	public function gdoTable(): GDO
	{
		return LUP_Room::table();
	}

	public function gdoHeaders(): array
	{
		$room = LUP_Room::table();
		return [
			$room->gdoColumn('room_enabled'),
			GDT_RoomEditID::make('room_id')->label('id'),
			$room->gdoColumn('room_name'),
            GDT_EditButton::make()->name('qrcode')->icon('qrcode'),
			$room->gdoColumn('room_color'),
			$room->gdoColumn('room_category'),
			$room->gdoColumn('room_www'),
		];
	}

	public function gdoQuery(): Query
	{
		$query = LUP_Room::table()->select();
		$user = GDO_User::current();
		if (!$user->isStaff())
		{
			$userId = $user->getID();
			$query->where("room_owner={$userId} OR room_id IN (SELECT work_room FROM lup_workers WHERE work_user={$userId})");
		}
		return $query;
	}

}
