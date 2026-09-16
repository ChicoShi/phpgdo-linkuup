<?php
namespace GDO\LinkUUp;

use GDO\Core\GDT_UInt;
use GDO\UI\GDT_Link;

final class GDT_RoomEditID extends GDT_UInt
{

	public function renderCell(): string
	{
		$room = $this->getGDO();
		return $room instanceof LUP_Room ?
			GDT_Link::anchor($room->href_edit(), (string)$room->getID()) :
			parent::renderCell();
	}

}
