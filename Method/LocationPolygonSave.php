<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Array;
use GDO\Core\GDT_Float;
use GDO\Core\GDT_Object;
use GDO\Core\MethodAjax;
use GDO\LinkUUp\LUP_Room;
use GDO\Maps\GDT_Polygon;

/** Saves one staff-edited room polygon. */
final class LocationPolygonSave extends MethodAjax
{
	public function getPermission(): ?string { return 'staff'; }

	public function isAlwaysTransactional(): bool { return true; }

	public function gdoParameters(): array
	{
		return [
			GDT_Object::make('room')->table(LUP_Room::table())->notNull(),
			GDT_Polygon::make('polygon')->notNull(),
			GDT_Float::make('view')->min(0.010)->max(42000.0),
			GDT_Float::make('lat')->min(-90)->max(90),
			GDT_Float::make('lng')->min(-180)->max(180),
		];
	}

	public function execute(): GDT
	{
		/** @var LUP_Room $room */
		$room = $this->gdoParameterValue('room');
		$polygon = $this->gdoParameterValue('polygon');
		$view = $this->gdoParameterValue('view');
		$lat = $this->gdoParameterValue('lat');
		$lng = $this->gdoParameterValue('lng');
		if (!$this->isPolygon($polygon))
		{
			return $this->error('err_parameter');
		}

		$values = ['room_polygon' => GDT_Polygon::encode($polygon)];
		if ($view !== null)
		{
			$values['room_view'] = (string)$view;
		}
		if ($lat !== null && $lng !== null)
		{
			$values['room_pos_lat'] = (string)$lat;
			$values['room_pos_lng'] = (string)$lng;
		}
		$room->saveVars($values);
		return GDT_Array::make()->value([
			'ok' => true,
			'room_id' => (int)$room->getID(),
		]);
	}

	private function isPolygon(mixed $polygon): bool
	{
		if (!is_array($polygon) || ($polygon['type'] ?? null) !== 'Polygon')
		{
			return false;
		}
		$ring = $polygon['coordinates'][0] ?? null;
		if (!is_array($ring) || count($ring) < 4 || $ring[0] !== $ring[array_key_last($ring)])
		{
			return false;
		}
		foreach ($ring as $point)
		{
			if (!is_array($point) || count($point) !== 2 || !is_numeric($point[0]) || !is_numeric($point[1]) ||
				$point[0] < -180 || $point[0] > 180 || $point[1] < -90 || $point[1] > 90)
			{
				return false;
			}
		}
		return true;
	}
}
