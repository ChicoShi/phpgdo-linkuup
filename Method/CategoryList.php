<?php
namespace GDO\LinkUUp\Method;

use GDO\Core\GDO;
use GDO\LinkUUp\LUP_Category;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_EditButton;

final class CategoryList extends MethodQueryTable
{

	public function gdoHeaders(): array
	{
		return array_merge([
			GDT_EditButton::make('edit'),
		], parent::gdoHeaders());
	}

	public function gdoTable(): GDO
	{
		return LUP_Category::table();
	}

	/**
	 * cat_parent points at this same table. Reusing one mutable row object via
	 * fetchInto() corrupts self-reference rendering on a populated page.
	 */
	public function useFetchInto(): bool
	{
		return false;
	}


}
