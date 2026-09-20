<?php
namespace GDO\LinkUUp\install;

use GDO\Address\GDO_Address;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;

/** The reviewed OSM batches are normal install seed data, not a local side job. */
final class LocationExpansion
{
	private const DATASETS = ['location-expansion', 'bar-expansion', 'location-expansion-2', 'location-expansion-3'];
	private const FIRST_ROOM_ID = 4000;
	/** Peine has its curated seed range; OSM-only venues continue after it. */
	private const FIRST_PEINE_ROOM_ID = 1202;

	public static function import(): void
	{
		$roomID = self::FIRST_ROOM_ID;
		$peineRoomID = self::FIRST_PEINE_ROOM_ID;
		foreach (self::DATASETS as $dataset)
		{
			$doc = json_decode(file_get_contents(Module_LinkUUp::instance()->filePath("data/{$dataset}/locations.json")), true, 512, JSON_THROW_ON_ERROR);
			$entries = $doc['entries'] ?? [];
			if (count($entries) !== 100 || count(array_unique(array_column($entries, 'key'))) !== 100)
			{
				throw new \RuntimeException("Invalid LinkUUp location dataset: {$dataset}");
			}
			foreach ($entries as $entry)
			{
				$genericRoomID = $roomID++;
				if (($entry['city'] ?? null) === 'Peine')
				{
					// Curated Peine seeds win over a matching generic OSM expansion.
					if (self::hasCuratedPeineDuplicate($entry))
					{
						continue;
					}
					self::importEntry($entry, $peineRoomID++);
				}
				else
				{
					self::importEntry($entry, $genericRoomID);
				}
			}
		}
	}

	/** @param array<string,mixed> $entry */
	private static function hasCuratedPeineDuplicate(array $entry): bool
	{
		$lat = (float)$entry['lat'];
		$lng = (float)$entry['lng'];
		return LUP_Room::table()->select()
			->joinObject('room_address')
			->where('room_id BETWEEN 1000 AND 1201')
			->where('room_name=' . LUP_Room::quoteS((string)$entry['name']))
			->where('address_city=' . LUP_Room::quoteS('Peine'))
			->where("(address_street=" . LUP_Room::quoteS((string)$entry['street']) .
				" OR (ABS(room_pos_lat-{$lat})<0.0003 AND ABS(room_pos_lng-{$lng})<0.0005))")
			->first()->exec()->fetchObject() !== null;
	}

	/** @param array<string,mixed> $entry */
	private static function importEntry(array $entry, int $roomID): void
	{
		$key = (string)($entry['key'] ?? '');
		if (!preg_match('/^osm-(node|way)-\d+$/', $key) || empty($entry['polygon']))
		{
			throw new \RuntimeException("Invalid LinkUUp location entry: {$key}");
		}
		// A former local-only import may already have allocated a different ID.
		if (LUP_Room::table()->select()->where('room_info LIKE ' . LUP_Room::quoteS('%[' . $key . ']%'))->first()->exec()->fetchObject())
		{
			return;
		}
		$name = (string)$entry['name'];
		$address = LocationRegistry::seedAddress((string)$roomID, [
			'address_name' => $name,
			'address_street' => (string)$entry['street'],
			'address_zip' => (string)$entry['zip'],
			'address_city' => (string)$entry['city'],
			'address_country' => 'DE',
		]);
		$category = (int)$entry['category'];
		$color = match ($category) { 3, 4, 5, 14 => '#E8B47F', 11 => '#E6A4DF', default => '#91BFF3' };
		$info = 'Quelle: OpenStreetMap contributors (ODbL). [' . $key . '] ' . $entry['source_url'] .
			' | Lokaler Test: Kreis innerhalb OSM-Gebäude; GPS-Abweichung möglich; Betreiber ungeprüft.';
		LUP_Room::blank([
			'room_id' => (string)$roomID,
			'room_owner' => GDO_User::getByName('shqiprim')->getID(),
			'room_name' => $name,
			'room_info' => $info,
			'room_color' => $color,
			'room_category' => (string)$category,
			'room_pos_lat' => (string)$entry['lat'],
			'room_pos_lng' => (string)$entry['lng'],
			'room_polygon' => json_encode($entry['polygon'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
			'room_view' => (string)$entry['view_km'],
			'room_radius' => (string)$entry['chat_radius_km'],
			'room_www' => (string)($entry['website'] ?? $entry['source_url']),
			'room_address' => $address->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}
}
