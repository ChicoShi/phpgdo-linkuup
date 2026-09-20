<?php
namespace GDO\LinkUUp\Websocket;

use GDO\Address\GDO_Address;
use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\LUPWS_Command;
use GDO\Maps\Position;
use GDO\Table\GDT_PageNum;
use GDO\UI\GDT_Page;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/**
 * Get a list of rooms within lat/lng.
 *
 * @author gizmore
 */
class LUPWS_RoomList extends LUPWS_Command
{

	public function execute(GWS_Message $msg)
	{
		$lat = $msg->readFloat();
		$lng = $msg->readFloat();
		$page = $msg->read32u();
        $perPage = $msg->read32u();

		if (!Position::isValidLat($lat))
		{
			return $msg->rplyError('err_latitude');
		}

		if (!Position::isValidLng($lng))
		{
			return $msg->rplyError('err_longitude');
		}

		$from = ($page-1) * $perPage;
		$query = LUP_Room::queryRooms(
			$lat,
			$lng,
			$perPage,
			$from,
		);
		// Counting must not mutate the paged room query. Query's fluent methods
		// operate in place, so use a copy before dropping its LIMIT and ORDER.
		$total = (int)$query->copy()->selectOnly('COUNT(*)')->noLimit()->noOrder()->noJoins()->exec()->fetchVar();
		$rooms = $query->exec()->fetchAllObjects();
		$addressIds = [];
		foreach ($rooms as $room)
		{
			if ($addressId = (int)$room->gdoVar('room_address'))
			{
				$addressIds[$addressId] = $addressId;
			}
		}
		$addresses = [];
		if ($addressIds)
		{
			$addressResult = GDO_Address::table()->select()
				->where('address_id IN (' . implode(',', $addressIds) . ')')
				->exec();
			while ($address = $addressResult->fetchObject())
			{
				$addresses[$address->getID()] = $address;
			}
		}

		$response = GWS_Message::wr32($total);

		foreach ($rooms as $room)
		{
			$room instanceof LUP_Room;
			$response .= $this->gdoToBinary($room);
			$addressId = $room->gdoVar('room_address');
			$response .= $this->gdoToBinary($addresses[$addressId] ?? GDO_Address::blank());
			$response .= LUP_Global::userListPayloadData($room, false);
			$response .= $msg->wr32(0);
		}
		$msg->replyBinary($msg->cmd(), $response);
	}

	private function roomUserList(LUP_Room $room)
	{
		$response = '';
		foreach (LUP_Global::$ROOM_USERS[$room->getID()] as $user)
		{
			$response .= GWS_Message::wr32($user->getID());
		}
		$response .= GWS_Message::wr32(0);
		return $response;
	}

}

GWS_Commands::register(0x1101, new LUPWS_RoomList());
