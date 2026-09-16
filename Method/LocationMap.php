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
		if ($user->isStaff() || ($room && $room->canEdit($user)))
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
		$this->getModule()->addJS('js/lup-location-map.js');

		$locations = [];
		$user = GDO_User::current();
		foreach (LUP_Room::table()->select()->order('room_id ASC')->exec()->fetchAllArray2dObject() as $room)
		{
			/** @var LUP_Room $room */
			if (!$user->isStaff() && !$room->canEdit($user))
			{
				continue;
			}
			$polygon = json_decode((string)$room->gdoVar('room_polygon'), true);
			if (!is_array($polygon))
			{
				$polygon = json_decode(GDT_Polygon::fromRadius($room->getLat(), $room->getLng(), $room->getRadius()), true);
			}
			$locations[] = [
				'id' => (int)$room->getID(),
				'name' => $room->getName(),
				'lat' => $room->getLat(),
				'lng' => $room->getLng(),
				'radius_km' => $room->getRadius(),
				'view_km' => (float)$room->gdoVar('room_view'),
				'color' => $room->getColor(),
				'polygon' => $polygon,
			];
		}

		$selectedRoom = $this->gdoParameterValue('room');
		Javascript::addJSPreInline('window.LUP_LOCATION_MAP = ' . json_encode([
			'locations' => $locations,
			'saveUrl' => href('LinkUUp', 'LocationPolygonSave'),
			'selectedRoom' => $selectedRoom ? (int)$selectedRoom->getID() : null,
		], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';');

		return $this->templatePHP('page/location_map.php');
	}
}
