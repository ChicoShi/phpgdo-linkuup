<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDO;
use GDO\Core\MethodCompletion;
use GDO\DB\Query;
use GDO\LinkUUp\LUP_Room;
use GDO\User\GDO_User;

/** Completion backed by the standard GDT_SearchField request parameter. */
final class RoomCompletion extends MethodCompletion
{
	protected function gdoTable(): GDO
	{
		return LUP_Room::table();
	}

	protected function getQuery(): Query
	{
		$query = LUP_Room::table()->select();
		$user = GDO_User::current();
		if (!$user->isStaff())
		{
			$userID = (int)$user->getID();
			$query->where("room_owner={$userID} OR room_id IN (SELECT work_room FROM lup_workers WHERE work_user={$userID})");
		}
		return $query;
	}

	protected function gdoHeaderFields(): array
	{
		$columns = LUP_Room::table()->gdoColumnsCache();
		return [
			'room_id' => $columns['room_id'],
			'room_name' => $columns['room_name'],
		];
	}

	public function itemToCompletionJSON(GDO $item): array
	{
		/** @var LUP_Room $item */
		return [
			'id' => $item->getID(),
			'text' => $item->renderName(),
			'display' => sprintf('#%s – %s', $item->getID(), $item->renderOption()),
		];
	}
}
