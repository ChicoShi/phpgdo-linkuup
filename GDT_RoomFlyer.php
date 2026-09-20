<?php
namespace GDO\LinkUUp;

use GDO\Core\GDT_String;
use GDO\UI\GDT_Link;

/** A per-row print action for the room overview. */
final class GDT_RoomFlyer extends GDT_String
{
	public function renderCell(): string
	{
		$room = $this->getGDO();
		return $room instanceof LUP_Room
			? GDT_Link::make()->href(href('LinkUUp', 'RoomFlyer', '&room=' . $room->getID() . '&_ajax=1'))->icon('print')->text('room_flyer')->render()
			: '';
	}
}
