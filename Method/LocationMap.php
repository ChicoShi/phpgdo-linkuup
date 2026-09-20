<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\Javascript;
use GDO\LinkUUp\LUP_Room;
use GDO\Maps\GDT_Polygon;
use GDO\User\GDO_User;

/** Staff map for reviewing and editing room geofences. */
final class LocationMap extends \GDO\Core\Method
{
	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$room = $this->gdoParameterValue('room');
		if ($user->isStaff() || ($room && $room->canEdit($user)) || LUP_Room::getEditableRooms($user))
		{
			return true;
		}
		$error = 'err_not_allowed';
		return false;
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('room')->table(LUP_Room::table()),
		];
	}

	public function execute(): GDT
	{
		$this->getModule()->addCSS('css/lup-location-map.css');
		$this->getModule()->addJS('js/lup-location-map.js?rev=20260918_1');

		$selectedRoom = $this->gdoParameterValue('room');
		$roomSearch = GDT_Object::make('room_search')
			->table(LUP_Room::table())
			->label('Location')
			->min(2)
			->completionHref(href('LinkUUp', 'RoomCompletion', '&_fmt=json'));
		if ($selectedRoom)
		{
			$roomSearch->value($selectedRoom);
		}
		Javascript::addJSPreInline('window.LUP_LOCATION_MAP = ' . json_encode([
			'saveUrl' => href('LinkUUp', 'LocationPolygonSave'),
			'roomUrl' => href('LinkUUp', 'LocationMapRoom', '&_fmt=json'),
			'selectedRoom' => $selectedRoom ? (int)$selectedRoom->getID() : null,
		], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';');

		return $this->templatePHP('page/location_map.php', ['roomSearch' => $roomSearch]);
	}

	/** Serialize exactly the editable map data required for one room. */
	public static function mapRoomJSON(LUP_Room $room): array
	{
		$lat = $room->gdoVar('room_pos_lat');
		$lng = $room->gdoVar('room_pos_lng');
		$hasPosition = $lat !== null && $lat !== '' && $lng !== null && $lng !== '';
		$polygon = json_decode((string)$room->gdoVar('room_polygon'), true);
		if (!is_array($polygon) && $hasPosition)
		{
			$polygon = json_decode(GDT_Polygon::fromRadius($room->getLat(), $room->getLng(), $room->getRadius()), true);
		}
		return [
			'id' => (int)$room->getID(),
			'name' => $room->getName(),
			'lat' => $hasPosition ? $room->getLat() : null,
			'lng' => $hasPosition ? $room->getLng() : null,
			'needs_current_position' => !$hasPosition,
			'radius_km' => $room->getRadius(),
			'view_km' => (float)$room->gdoVar('room_view'),
			'color' => $room->getColor(),
			'polygon' => $polygon,
		];
	}
}
