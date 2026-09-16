<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\User\GDO_User;

/**
 * Country-level LinkUUp seed data for South Korea.
 *
 * OSM reference: administrative relation 307756 (ISO3166-1=KR), checked
 * 2026-09-16. The pin is the relation's OSM centre. The outline is the
 * largest OSM land ring reduced to 32 vertices for a deliberately compact
 * national chat geofence; it does not include North Korean territory.
 */
final class InstallKoreaSeeds
{
	/** @param array<int, object> $icons Installed LinkUUp image files. */
	public static function seed(array $icons): void
	{
		LUP_Room::blank([
			'room_id' => '82000',
			'room_owner' => GDO_User::getByName('gizmore')->getID(),
			'room_name' => 'South Korea',
			'room_info' => 'South Korea chat for people discovering LinkUUp in Korea.',
			'room_color' => '#0F4C81',
			'room_category' => '2',
			'room_sort' => '82',
			'room_enabled' => '1',
			'room_pos_lat' => '36.6383920',
			'room_pos_lng' => '127.6961188',
			'room_view' => '1000000',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[124.8364258,34.1091822],[125.0608056,33.8521703],[126.0590716,33.6998995],[125.925417,33.2318111],[126.227026,32.910751],[127.0129391,33.168307],[127.1924969,33.5968912],[126.9153195,33.7591991],[127.6624328,33.8360101],[127.9990615,34.1233011],[128.8162752,34.343261],[128.7927232,34.5099527],[129.2665806,35.1337097],[129.6690341,35.3999801],[129.8326489,35.9927301],[129.6299898,36.2875404],[129.7304631,36.7758398],[129.6243797,37.2710398],[128.8692852,38.2144463],[128.6559809,38.61772],[128.3095036,38.593558],[128.0546983,38.3062812],[127.0421478,38.258813],[126.6626179,37.7807247],[126.2060938,37.8232481],[126.0144365,37.67998],[125.4263063,37.6492146],[125.8568388,37.3992187],[125.3046323,36.6975887],[125.7508847,36.0149494],[125.7760229,35.4497421],[125.0057183,34.8522416],[124.8364258,34.1091822]]]}',
			'room_radius' => '600',
			'room_www' => 'https://www.openstreetmap.org/relation/307756',
			'room_icon' => $icons[0]->getID(),
			'room_image' => $icons[0]->getID(),
			'room_show_distance' => '0',
		])->softReplace();
	}
}
