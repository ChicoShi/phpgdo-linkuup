<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\Website;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\UI\MethodPage;

/**
 * Public, server-rendered directory for locations explicitly approved by staff.
 */
final class PublicLocations extends MethodPage
{
	public function gdoParameters(): array
	{
		return [GDT_Object::make('room')->table(LUP_Room::table())];
	}

	public function getMethodTitle(): string
	{
		if ($room = $this->getPublicRoom())
		{
			return t('mt_linkuup_publiclocations_room', [html($room->getName())]);
		}
		return t('mt_linkuup_publiclocations');
	}

	public function execute(): GDT
	{
		if ($room = $this->getPublicRoom())
		{
			$this->addPlaceSchema($room);
		}
		return parent::execute();
	}

	protected function getTemplateVars(): array
	{
		$room = $this->getPublicRoom();
		return [
			'room' => $room,
			'rooms' => $room ? [] : LUP_Room::queryPublicRooms()->fetchAll(),
			'appURL' => Module_LinkUUp::instance()->cfgAppUrl(),
		];
	}

	private function getPublicRoom(): ?LUP_Room
	{
		$room = LUP_Room::paramFrom($this, 'room');
		return $room && $room->isPublic() ? $room : null;
	}

	private function addPlaceSchema(LUP_Room $room): void
	{
		$address = $room->getAddress();
		$schema = [
			'@context' => 'https://schema.org',
			'@type' => 'Place',
			'name' => $room->getName(),
			'description' => $room->getInfo(),
			'url' => url('LinkUUp', 'PublicLocations', '&room=' . $room->getID()),
			'geo' => [
				'@type' => 'GeoCoordinates',
				'latitude' => $room->getLat(),
				'longitude' => $room->getLng(),
			],
		];
		if ($address && !$address->emptyAddress())
		{
			$schema['address'] = array_filter([
				'@type' => 'PostalAddress',
				'streetAddress' => $address->getStreet(),
				'postalCode' => $address->getZIP(),
				'addressLocality' => $address->getCity(),
				'addressCountry' => $address->getCountryID(),
			]);
		}
		Website::addHead('<script type="application/ld+json">' .
			json_encode($schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) .
			'</script>');
	}
}
