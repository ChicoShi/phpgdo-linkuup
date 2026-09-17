<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\User\GDO_User;

/**
 * Country-level LinkUUp seed data for South Korea.
 *
 * OSM reference: administrative relation 307756 (ISO3166-1=KR), checked
 * 2026-09-16. The pin is the verified Gyeongin National University of
 * Education test point in Incheon, supplied by the local tester. The outline is the
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
			'room_pos_lat' => '37.5382',
			'room_pos_lng' => '126.7163',
			'room_view' => '1000000',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[124.8364258,34.1091822],[125.0608056,33.8521703],[126.0590716,33.6998995],[125.925417,33.2318111],[126.227026,32.910751],[127.0129391,33.168307],[127.1924969,33.5968912],[126.9153195,33.7591991],[127.6624328,33.8360101],[127.9990615,34.1233011],[128.8162752,34.343261],[128.7927232,34.5099527],[129.2665806,35.1337097],[129.6690341,35.3999801],[129.8326489,35.9927301],[129.6299898,36.2875404],[129.7304631,36.7758398],[129.6243797,37.2710398],[128.8692852,38.2144463],[128.6559809,38.61772],[128.3095036,38.593558],[128.0546983,38.3062812],[127.0421478,38.258813],[126.6626179,37.7807247],[126.2060938,37.8232481],[126.0144365,37.67998],[125.4263063,37.6492146],[125.8568388,37.3992187],[125.3046323,36.6975887],[125.7508847,36.0149494],[125.7760229,35.4497421],[125.0057183,34.8522416],[124.8364258,34.1091822]]]}',
			'room_radius' => '600',
			'room_www' => 'https://www.openstreetmap.org/relation/307756',
			'room_icon' => $icons[0]->getID(),
			'room_image' => $icons[0]->getID(),
			'room_show_distance' => '0',
		])->softReplace();

		// OSM way 227365995, amenity=university, name:en=Gyeongin National
		// University of Education Incheon Campus. The tester's pin is inside
		// the mapped campus boundary.
		LUP_Room::blank([
			'room_id' => '82001',
			'room_owner' => GDO_User::getByName('gizmore')->getID(),
			'room_name' => 'Gyeongin National University of Education – Incheon Campus',
			'room_info' => 'Campus chat for students and visitors at Gyeongin National University of Education in Incheon.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_sort' => '83',
			'room_enabled' => '1',
			'room_pos_lat' => '37.5382',
			'room_pos_lng' => '126.7163',
			'room_view' => '2',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[126.7152197,37.5393965],[126.7148603,37.5394199],[126.7147047,37.5394922],[126.7142777,37.5394863],[126.7142876,37.5399725],[126.7144446,37.540056],[126.7145948,37.5399261],[126.7146806,37.5399029],[126.7152868,37.5400816],[126.7161531,37.5404771],[126.7163194,37.5405154],[126.7163248,37.5406067],[126.7163972,37.540626],[126.7164777,37.5404216],[126.7167083,37.5403113],[126.716703,37.5402281],[126.7168424,37.5401919],[126.7169497,37.5400154],[126.7171665,37.5398674],[126.7171203,37.5394688],[126.7171536,37.5394435],[126.7180038,37.5393925],[126.7179993,37.539256],[126.7176498,37.5390607],[126.7175318,37.5390371],[126.7175371,37.5389393],[126.7177759,37.5386266],[126.7180012,37.5385354],[126.7180307,37.5384565],[126.7181621,37.5383608],[126.7182157,37.5383737],[126.7183391,37.5384777],[126.7184105,37.5384763],[126.7185832,37.5383822],[126.718808,37.538382],[126.7190858,37.5381193],[126.7193035,37.5379486],[126.7192704,37.537808],[126.7192162,37.5378227],[126.719117,37.5374761],[126.7194791,37.5373656],[126.719471,37.5371592],[126.719243,37.5369188],[126.7192242,37.5367295],[126.7193852,37.536534],[126.7198036,37.536317],[126.7198063,37.5356852],[126.7194147,37.5356725],[126.7184142,37.5348174],[126.7176498,37.5345282],[126.7176364,37.5342389],[126.7175076,37.5342729],[126.7174402,37.5342697],[126.7173616,37.534266],[126.7173036,37.5342632],[126.7172098,37.533959],[126.7169541,37.534],[126.7168315,37.5346624],[126.7169708,37.5347859],[126.717057,37.5360058],[126.7152599,37.5376206],[126.7150744,37.5388854],[126.715217,37.539099],[126.7152197,37.5393965]]]}',
			'room_radius' => '0.5',
			'room_www' => 'https://www.openstreetmap.org/way/227365995',
			'room_icon' => $icons[0]->getID(),
			'room_image' => $icons[0]->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}
}
