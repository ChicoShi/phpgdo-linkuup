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
			GDT_Link::make()->href($room->href_edit())->textRaw((string)$room->getID())->icon('edit')->render() :
			parent::renderCell();
	}

}
