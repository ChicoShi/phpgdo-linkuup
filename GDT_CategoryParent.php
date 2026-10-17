<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;

/**
 * A self-referencing category parent needs a cell renderer which always uses
 * the raw foreign key of the current table row.
 */
final class GDT_CategoryParent extends GDT_Object
{

	public function renderCell(): string
	{
		$parentID = $this->getVar();
		if (!is_string($parentID) || ($parentID === ''))
		{
			return GDT::EMPTY_STRING;
		}

		$parent = LUP_Category::getById($parentID);
		return $parent ? $parent->renderHTML() : GDT::EMPTY_STRING;
	}

}
