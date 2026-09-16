<?php
namespace GDO\LinkUUp;

/**
 * A LinkUUp location selector.
 *
 * Locations are stored as rooms internally; this field gives location-facing
 * pages a domain-specific label while retaining the same selection rules.
 */
final class GDT_LocationSelect extends GDT_RoomSelect
{
	public function gdtDefaultLabel(): ?string
	{
		return 'location';
	}
}
