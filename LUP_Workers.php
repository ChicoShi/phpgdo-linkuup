<?php
namespace GDO\LinkUUp;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Object;
use GDO\DB\Result;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/** A user's employee assignment for one LinkUUp room. */
final class LUP_Workers extends GDO
{
	public static function addWorker(LUP_Room $room, GDO_User $user): self
	{
		if ($worker = self::getWorker($room, $user))
		{
			return $worker;
		}
		return self::blank([
			'work_room' => $room->getID(),
			'work_user' => $user->getID(),
		])->insert();
	}

	public static function isWorker(LUP_Room $room, GDO_User $user): bool
	{
		return self::getWorker($room, $user) !== null;
	}

	public static function removeWorker(LUP_Room $room, GDO_User $user): Result
	{
		return self::table()->deleteWhere("work_room={$room->getID()} AND work_user={$user->getID()}")->exec();
	}

	private static function getWorker(LUP_Room $room, GDO_User $user): ?self
	{
		return self::fetchFrom(self::table()->select()
			->where("work_room={$room->getID()} AND work_user={$user->getID()}")
			->first()->exec());
	}

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('work_id'),
			GDT_Object::make('work_room')->table(LUP_Room::table())->notNull()->cascade(),
			GDT_User::make('work_user')->notNull()->cascade(),
			GDT_CreatedAt::make('work_created'),
			GDT_CreatedBy::make('work_creator'),
		];
	}

	/** @return GDO_User[] */
	public function getCoworkers(LUP_Room $room): array
	{
		return $this->getCoworkersResult($room)->fetchAllObjects();
	}

	public function getCoworkersResult(LUP_Room $room): Result
	{
		return $this->select('work_user_t.*')
			->where("work_room={$room->getID()}")
			->joinObject('work_user')
			->fetchTable(GDO_User::table())
			->exec();
	}
}
