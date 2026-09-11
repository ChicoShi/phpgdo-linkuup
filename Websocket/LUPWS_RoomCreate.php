<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Websocket;

use GDO\LinkUUp\LUP_Category;
use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\LUP_RoomWorker;
use GDO\LinkUUp\LUPWS_Command;
use GDO\Maps\GDT_Polygon;
use GDO\User\GDO_UserPermission;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/** Create the deliberately small, GPS-backed VIP room form from the app. */
final class LUPWS_RoomCreate extends LUPWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$user = $msg->user();
		if (!LUP_Global::isVIP($user))
		{
			return $msg->rplyError('err_lup_vip_only');
		}

		$name = trim($msg->readString());
		$categoryId = $msg->read32u();
		$info = trim($msg->readString());
		$lat = $msg->readFloat();
		$lng = $msg->readFloat();
		$radiusMeters = $msg->readFloat();
		$polygon = $msg->hasMore() ? $this->readPolygon($msg) : null;
		$viewRadiusKm = $msg->hasMore() ? $msg->readFloat() : 1.5;

		if ($name === '' || strlen($name) > LUP_Room::MAX_ROOM_NAME_LEN)
		{
			return $msg->rplyError('err_lup_room_name');
		}
		if (strlen($info) > 512)
		{
			return $msg->rplyError('err_lup_room_info');
		}
		if ((!is_finite($lat)) || (!is_finite($lng)) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180)
		{
			return $msg->rplyError('err_lup_room_position');
		}
		if ($radiusMeters < 150 || $radiusMeters > 500)
		{
			return $msg->rplyError('err_lup_room_radius');
		}
		if (!LUP_Category::getById((string)$categoryId))
		{
			return $msg->rplyError('err_lup_room_category');
		}
		if ($polygon === false)
		{
			return $msg->rplyError('err_lup_room_polygon');
		}
		if (!is_finite($viewRadiusKm) || $viewRadiusKm < 0.010 || $viewRadiusKm > 42000.0)
		{
			return $msg->rplyError('err_lup_room_view');
		}
		/* A drawn boundary supersedes the legacy chat-radius field. */
		if (is_string($polygon))
		{
			$radiusMeters = max(1.0, GDT_Polygon::radiusFromCenter($polygon, $lat, $lng) * 1000);
		}

		$room = LUP_Room::blank([
			'room_owner' => $user->getID(),
			'room_name' => $name,
			'room_info' => $info,
			'room_category' => (string)$categoryId,
			'room_color' => '#6452c9',
			'room_pos_lat' => (string)$lat,
			'room_pos_lng' => (string)$lng,
			'room_view' => (string)$viewRadiusKm,
			'room_radius' => (string)($radiusMeters / 1000),
			'room_polygon' => $polygon,
		])->insert();

		LUP_RoomWorker::addWorker($room, $user);
		GDO_UserPermission::grant($user, 'lup_owner');
		$room = LUP_Global::refreshRoom($room->getID()) ?: $room;
		LUPWS_Room::broadcastRoomAdded($room);
		return $msg->replyBinary($msg->cmd(), GWS_Message::wrN(4, $room->getID()));
	}

	/** Read an optional, client-drawn GeoJSON polygon without weakening old app clients. */
	private function readPolygon(GWS_Message $msg): string|false|null
	{
		$json = trim($msg->readString());
		if ($json === '')
		{
			return null;
		}
		$polygon = json_decode($json, true);
		$ring = $polygon['coordinates'][0] ?? null;
		if (($polygon['type'] ?? null) !== 'Polygon' || !is_array($ring) || count($ring) < 4 || count($ring) > 65)
		{
			return false;
		}
		foreach ($ring as $point)
		{
			if (!is_array($point) || count($point) !== 2 || !is_numeric($point[0]) || !is_numeric($point[1]) ||
				(float)$point[0] < -180 || (float)$point[0] > 180 || (float)$point[1] < -90 || (float)$point[1] > 90)
			{
				return false;
			}
		}
		$first = $ring[0];
		$last = $ring[count($ring) - 1];
		if ((float)$first[0] !== (float)$last[0] || (float)$first[1] !== (float)$last[1])
		{
			return false;
		}
		return GDT_Polygon::encode($polygon);
	}
}

GWS_Commands::register(0x1165, new LUPWS_RoomCreate());
