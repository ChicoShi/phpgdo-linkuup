<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\User\GDO_User;

/**
 * Seed data for Peine.
 *
 * Keep Peine venues here rather than growing the generic installer further.
 * Each later entry needs a stable room/address ID, a checked pin, a category,
 * and a deliberately small chat radius.
 */
final class InstallPeine
{
	/** @param array<int, object>  Installed LinkUUp image files. */
	public static function seed(array $icons): void
	{
		self::seedGarage($icons);
		self::seedCityChat($icons);
		self::seedTownHall($icons);
		self::seedMogwai($icons);
		self::seedStandesamt($icons);
		self::seedEmploymentAgency($icons);
		self::seedHospital($icons);
		self::seedJobcenter($icons);
		self::seedSkatepark($icons);
		self::seedCemetery($icons);
		self::seedGunzelinSchool($icons);
		self::seedCityPark($icons);
		self::seedCityCentre($icons);
		self::seedMarketSquare($icons);
		self::seedStation($icons);
		self::seedSilberkampGymnasium($icons);
		self::seedWeitkowitz($icons);
		self::seedLandmarks($icons);
		self::seedRestaurants($icons);
		self::seedDoctors($icons);
	}

	private static function seedGarage(array $icons): void
	{
		$garage = LocationRegistry::seedAddress('1000', [
						'address_company' => null,
			'address_vat' => null,
			'address_name' => 'Garage Peine',
			'address_street' => 'Pulverturmval 68',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 725 29',
			'address_phone_fax' => null,
			'address_phone_mobile' => null,
			'address_email' => 'garage-peine@gmx.de',
		]);

		LUP_Room::blank([
			'room_id' => '1000',
			'room_owner' => GDO_User::getByName('gizmore')->getID(),
			'room_name' => 'Garage',
			'room_info' => 'Die Garage ist der Rock, Metal und Punk Szenetreff in Peine.',
			'room_color' => '#133742',
			'room_category' => '4',
			'room_pos_lat' => '52.32249832',
			'room_pos_lng' => '10.22929955',
			'room_view' => '0.983854',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2291611,52.3227237],[10.2293572,52.3227155],[10.2295557,52.3226501],[10.2296213,52.3224715],[10.2292684,52.322242],[10.2288202,52.3222976],[10.2288738,52.3225122],[10.2291611,52.3227237]]]}',
			'room_radius' => '0.2',
			'room_www' => 'https://www.facebook.com/garage.peine/',
			'room_phone' => '05171 79 120 53',
			'room_hours' => 'Tu-Su 18:00-03:00;',
			'room_address' => $garage->getID(),
			'room_icon' => $icons[1]->getID(),
			'room_image' => $icons[2]->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Weitkowitz GmbH, Woltorfer Straße 125. Reviewed local editor polygon. */
	private static function seedWeitkowitz(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1190', [
			'address_name' => 'Weitkowitz GmbH',
			'address_company' => 'Weitkowitz GmbH',
			'address_street' => 'Woltorfer Straße 125',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 70610',
			'address_email' => 'info@weitkowitz.de',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1190',
			'room_owner' => null,
			'room_name' => 'Weitkowitz GmbH',
			'room_info' => 'Hersteller für Kabelschuhe, Verbinder und Werkzeuge in Peine.',
			'room_color' => '#2B638C',
			'room_category' => '6',
			'room_pos_lat' => '52.3190000',
			'room_pos_lng' => '10.2554000',
			'room_view' => '0.340744',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2561662,52.3197486],[10.254364,52.3197552],[10.2546644,52.318337],[10.256295,52.3185665],[10.2561662,52.3197486]]]}',
			'room_radius' => '0.100',
			'room_www' => 'https://www.weitkowitz.de/',
			'room_phone' => '+49 5171 70610',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	private static function seedCityChat(array $icons): void
	{
		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '3',
			'room_owner' => null,
			'room_name' => 'Peine',
			'room_info' => 'Die Stadt Peine. Kennt man doch.',
			'room_color' => '#133742',
			'room_category' => '2',
			'room_sort' => '20',
			'room_enabled' => '1',
			'room_pos_lat' => '52.3204',
			'room_pos_lng' => '10.2341',
			// Discoverable regionally; participation remains local to Peine.
			'room_view' => '297.309',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.1534446,52.39657],[10.3104709,52.4101628],[10.3496097,52.3078329],[10.2976108,52.2597345],[10.2543226,52.2172516],[10.1965855,52.187766],[10.0975908,52.2264224],[10.1117748,52.3082527],[10.1570934,52.3460311],[10.1534446,52.39657]]]}',
			'room_radius' => '5',
			'room_www' => 'https://www.peine.de/',
			'room_phone' => null,
			'room_hours' => null,
			'room_address' => null,
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Rathaus, Bürgerbüro share this single physical location. */
	private static function seedTownHall(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1001', [
						'address_name' => 'Rathaus & Bürgerbüro Peine',
			'address_street' => 'Kantstraße 5',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 49-0',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1001',
			'room_owner' => null,
			'room_name' => 'Rathaus & Bürgerbüro',
			'room_info' => 'Stadtverwaltung Peine mit Bürgerbüro.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_pos_lat' => '52.32175064',
			'room_pos_lng' => '10.23231792',
			'room_view' => '0.70750403',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2325271,52.3220176],[10.232854,52.321789],[10.2329238,52.3214497],[10.2325217,52.321349],[10.2319533,52.3214562],[10.2318836,52.3220022],[10.2325271,52.3220176]]]}',
			'room_radius' => '0.075',
			'room_www' => 'https://www.peine.de/de/rathaus/stadtverwaltung.php',
			'room_phone' => '05171 49-0',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Development pin for local GPS and room-flow tests. */
	private static function seedMogwai(array $icons): void
	{
		$owner = GDO_User::getByName('gizmore');
		$address = LocationRegistry::seedAddress('1002', [
						'address_name' => 'LinkUUp Dev-Standort',
			'address_street' => 'Am Bauhof 15',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		LUP_Room::blank([
			'room_id' => '1002',
			'room_owner' => $owner->getID(),
			'room_name' => 'Mogwai',
			'room_info' => 'Wohnsitz eines LinkUUp-Programmierers.',
			'room_color' => '#4DBB94',
			'room_category' => '6',
			'room_pos_lat' => '52.32382202',
			'room_pos_lng' => '10.24394798',
			'room_view' => '0.10499468',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2439064,52.3240495],[10.2442282,52.3238675],[10.24396,52.3236757],[10.243665,52.3238528],[10.2439064,52.3240495]]]}',
			'room_radius' => '0.050',
			'room_address' => $address->getID(),
			'room_icon' => null,
			'room_image' => null,
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Local LinkUUp test point near the Woltorfer Straße business area. */
	private static function seedStandesamt(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1003', [
						'address_name' => 'Standesamt',
			'address_street' => 'Woltorfer Straße 77 B',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1003',
			'room_owner' => null,
			'room_name' => 'Standesamt',
			'room_info' => 'LinkUUp-Testort an der Woltorfer Straße.',
			'room_color' => '#3D6CC9',
			'room_category' => '6',
			'room_pos_lat' => '52.32092667',
			'room_pos_lng' => '10.24682140',
			'room_view' => '0.42739126',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2468433,52.3213504],[10.247326,52.3212157],[10.2472295,52.3206946],[10.2467897,52.3203572],[10.246028,52.3206395],[10.2460496,52.3210366],[10.2463606,52.3213312],[10.2468433,52.3213504]]]}',
			'room_radius' => '0.075',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Agentur für Arbeit Peine, Im Schleusenteich 1. */
	private static function seedEmploymentAgency(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1004', [
						'address_name' => 'Agentur für Arbeit Peine',
			'address_street' => 'Im Schleusenteich 1',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 7740-62',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1004',
			'room_owner' => null,
			'room_name' => 'Arbeitsamt Peine',
			'room_info' => 'Agentur für Arbeit Peine.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_pos_lat' => '52.32102585',
			'room_pos_lng' => '10.23891544',
			'room_view' => '0.13819110',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2387759,52.3212838],[10.2392856,52.3211697],[10.2393742,52.3209224],[10.2393125,52.320649],[10.2389586,52.3206169],[10.2384919,52.3207069],[10.2385187,52.3210265],[10.2386258,52.3211617],[10.2387759,52.3212838]]]}',
			'room_radius' => '0.177',
			'room_www' => 'https://www.arbeitsagentur.de/vor-ort/hildesheim/peine',
			'room_phone' => '05171 7740-62',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Klinikum Peine, Virchowstraße 8h. OSM way/26989261. */
	private static function seedHospital(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1201', [
			'address_name' => 'Klinikum Peine',
			'address_street' => 'Virchowstraße 8h',
			'address_zip' => '31226',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 93-0',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1201',
			'room_owner' => null,
			'room_name' => 'Klinikum Peine',
			'room_info' => 'Krankenhaus und medizinisches Zentrum in Peine.',
			'room_color' => '#B64368',
			'room_category' => '18',
			'room_enabled' => '1',
			'room_pos_lat' => '52.301381',
			'room_pos_lng' => '10.239061',
			'room_view' => '0.250',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2357708,52.3033867],[10.2368866,52.3034234],[10.239686,52.3035307],[10.240187,52.303533],[10.242306,52.3032181],[10.2423562,52.3023833],[10.2400556,52.3023289],[10.2399614,52.3023267],[10.2401466,52.2992319],[10.2359958,52.2998406],[10.2357708,52.3033867]]]}',
			'room_radius' => '0.250',
			'room_www' => 'https://www.klinikum-peine.de/',
			'room_phone' => '05171 93-0',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Jobcenter Landkreis Peine, Stederdorfer Straße 24. OSM way/478979871. */
	private static function seedJobcenter(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1200', [
			'address_name' => 'Jobcenter Landkreis Peine',
			'address_street' => 'Stederdorfer Straße 24',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 401 7780',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1200',
			'room_owner' => null,
			'room_name' => 'Jobcenter Peine',
			'room_info' => 'Jobcenter Landkreis Peine.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_enabled' => '1',
			'room_pos_lat' => '52.3250778',
			'room_pos_lng' => '10.2254629',
			'room_view' => '0.050',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2252801,52.3251455],[10.2254337,52.3250101],[10.2255677,52.3248683],[10.2257539,52.3249296],[10.2254132,52.3252025],[10.2252801,52.3251455]]]}',
			'room_radius' => '0.075',
			'room_www' => 'https://www.landkreis-peine.de/Soziales-Bildung/Jobcenter/',
			'room_phone' => '05171 401 7780',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Public skate and inline park at the Unternehmenspark Peine II. */
	private static function seedSkatepark(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1005', [
						'address_name' => 'Skatepark Peine',
			'address_street' => 'Hans-Gallinis-Straße',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1005',
			'room_owner' => null,
			'room_name' => 'Skatepark Peine',
			'room_info' => 'Öffentliche Skate- und Inlineranlage am Unternehmenspark Peine II.',
			'room_color' => '#2D946A',
			'room_category' => '13',
			'room_pos_lat' => '52.32170105',
			'room_pos_lng' => '10.24289989',
			'room_view' => '0.16567884',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2425213,52.3213899],[10.2431285,52.3213177],[10.2432894,52.3218856],[10.2431001,52.321952],[10.2429107,52.321979],[10.2425964,52.3218888],[10.2425213,52.3213899]]]}',
			'room_radius' => '0.150',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Evangelischer St.-Jakobi-Friedhof, Gunzelinstraße 31. */
	private static function seedCemetery(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1006', [
						'address_name' => 'St.-Jakobi-Friedhof Peine',
			'address_street' => 'Gunzelinstraße 31',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 6116',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1006',
			'room_owner' => null,
			'room_name' => 'Friedhof Peine',
			'room_info' => 'Evangelischer St.-Jakobi-Friedhof an der Gunzelinstraße.',
			'room_color' => '#66706D',
			'room_category' => '8',
			'room_pos_lat' => '52.32730865',
			'room_pos_lng' => '10.23902798',
			'room_view' => '0.43284246',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2381929,52.3263992],[10.2401428,52.3263074],[10.2419989,52.3262926],[10.2419727,52.326968],[10.2417964,52.328994],[10.2376886,52.3288498],[10.2381929,52.3263992]]]}',
			'room_radius' => '0.100',
			'room_www' => 'https://www.stjakobi-peine.de/Ueber-uns/Friedhof',
			'room_phone' => '05171 6116',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Gunzelin-Realschule, Gunzelinstraße 42. */
	private static function seedGunzelinSchool(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1007', [
						'address_name' => 'Gunzelin-Realschule',
			'address_street' => 'Gunzelinstraße 42',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 7902710',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1007',
			'room_owner' => null,
			'room_name' => 'Gunzelin-Realschule',
			'room_info' => 'Ganztags-Realschule in Peine.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_pos_lat' => '52.32564926',
			'room_pos_lng' => '10.24110126',
			'room_view' => '0.2445264',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.240722,52.3249337],[10.2417063,52.3249238],[10.2421462,52.3260664],[10.2414797,52.3260828],[10.2406416,52.3260926],[10.240722,52.3249337]]]}',
			'room_radius' => '0.100',
			'room_www' => 'https://rs-peine.de/',
			'room_phone' => '05171 7902710',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Stadtpark Peine, including the pond and minigolf grounds. */
	private static function seedCityPark(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1008', [
						'address_name' => 'Stadtpark Peine',
			'address_street' => 'Kantstraße',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1008',
			'room_owner' => null,
			'room_name' => 'Stadtpark Peine',
			'room_info' => 'Stadtpark mit See, Minigolf und Sitzmöglichkeiten.',
			'room_color' => '#2D946A',
			'room_category' => '20',
			'room_pos_lat' => '52.32157135',
			'room_pos_lng' => '10.23580933',
			'room_view' => '0.46462232',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2346386,52.3208519],[10.2372152,52.3207601],[10.2372313,52.3214066],[10.239471,52.3213429],[10.2394146,52.3221973],[10.2342095,52.3223153],[10.2346386,52.3208519]]]}',
			// Roughly the radius of the 40,000 m² park grounds.
			'room_radius' => '0.125',
			'room_www' => 'https://www.peine.de/de/rathaus/stadtportraet/stadtrundgang/Stadtpark.php',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Peine city centre, centred at St.-Jakobi-Kirche. */
	private static function seedCityCentre(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1009', [
						'address_name' => 'St.-Jakobi-Kirche',
			'address_street' => 'Breite Straße 14',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1009',
			'room_owner' => null,
			'room_name' => 'Peiner Innenstadt',
			'room_info' => 'Die Peiner Innenstadt rund um St. Jakobi.',
			'room_color' => '#3D6CC9',
			'room_category' => '2',
			'room_pos_lat' => '52.32228851',
			'room_pos_lng' => '10.22737026',
			'room_view' => '2.08183551',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2263144,52.3157342],[10.23385,52.3160753],[10.2327342,52.3250358],[10.2268743,52.3267309],[10.2207998,52.3250948],[10.2214113,52.3229592],[10.2263144,52.3157342]]]}',
			'room_radius' => '0.600',
			'room_www' => 'https://www.stjakobi-peine.de/',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Historic market square in the pedestrian zone. */
	private static function seedMarketSquare(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1010', [
						'address_name' => 'Marktplatz Peine',
			'address_street' => 'Am Markt',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1010',
			'room_owner' => null,
			'room_name' => 'Peiner Marktplatz',
			'room_info' => 'Historischer Marktplatz in der Peiner Fußgängerzone.',
			'room_color' => '#C9821E',
			'room_category' => '2',
			'room_pos_lat' => '52.32329559',
			'room_pos_lng' => '10.22571182',
			'room_view' => '0.2',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2251343,52.3230533],[10.2261561,52.3229582],[10.226435,52.3233696],[10.2258893,52.3237762],[10.2250699,52.3236483],[10.2248715,52.3232984],[10.2251343,52.3230533]]]}',
			'room_radius' => '0.100',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Deutsche Bahn station at Bahnhofsplatz 1. */
	private static function seedStation(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1011', [
						'address_name' => 'Bahnhof Peine',
			'address_street' => 'Bahnhofsplatz 1',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1011',
			'room_owner' => null,
			'room_name' => 'Peine Bahnhof',
			'room_info' => 'Bahnhof Peine mit Regionalzug- und Busanschluss.',
			'room_color' => '#4E6F92',
			'room_category' => '2',
			'room_pos_lat' => '52.31914902',
			'room_pos_lng' => '10.23225021',
			'room_view' => '0.30000001',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.230988,52.3186259],[10.2338266,52.3186259],[10.233215,52.3195347],[10.2308485,52.3194954],[10.230988,52.3186259]]]}',
			'room_radius' => '0.150',
			'room_www' => 'https://www.bahnhof.de/peine',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Gymnasium am Silberkamp, the larger local school campus. */
	private static function seedSilberkampGymnasium(array $icons): void
	{
		$address = LocationRegistry::seedAddress('1012', [
						'address_name' => 'Gymnasium am Silberkamp',
			'address_street' => 'Am Silberkamp 30',
			'address_zip' => '31224',
			'address_city' => 'Peine',
			'address_country' => 'DE',
			'address_phone' => '+49 5171 4019500',
		]);

		$image = $icons[3];
		LUP_Room::blank([
			'room_id' => '1012',
			'room_owner' => null,
			'room_name' => 'Gymnasium am Silberkamp',
			'room_info' => 'Gymnasium am Silberkamp in Peine.',
			'room_color' => '#3D6CC9',
			'room_category' => '16',
			'room_pos_lat' => '52.32259369',
			'room_pos_lng' => '10.23928547',
			'room_view' => '0.60148644',
			'room_polygon' => '{"type":"Polygon","coordinates":[[[10.2385136,52.3222636],[10.2397506,52.3221718],[10.2424897,52.3220275],[10.2424843,52.3225574],[10.2410386,52.3227272],[10.2403332,52.3230938],[10.2393054,52.3232577],[10.238578,52.3231069],[10.2385136,52.3222636]]]}',
			'room_radius' => '0.150',
			'room_www' => 'https://www.silberkamp.de/',
			'room_phone' => '05171 4019500',
			'room_address' => $address->getID(),
			'room_icon' => $image->getID(),
			'room_image' => $image->getID(),
			'room_show_distance' => '1',
		])->softReplace();
	}

	/** Public cultural, event, and outdoor landmarks beyond the core centre. */
	private static function seedLandmarks(array $icons): void
	{
		$landmarks = [
			['Kreismuseum Peine', 'Das Kreismuseum mit Dauer- und Sonderausstellungen.', '12', 52.3248575, 10.2253998, 'Stederdorfer Straße 17', '31224', '#835DC2', '0.075', 'https://www.landkreis-peine.de/Themen-Projekte/Kreismuseum/', null, '10.0'],
			['Stadtbücherei Peine', 'Stadtbücherei im Zentrum von Peine.', '12', 52.3207687, 10.2263743, 'Winkel 30A', '31224', '#835DC2', '0.075', null, '{"type":"Polygon","coordinates":[[[10.226159,52.3208207],[10.2261809,52.3206909],[10.2265897,52.3207167],[10.2265677,52.3208465],[10.226159,52.3208207]]]}', '10.0'],
			['Stadttheater Peiner Festsäle', 'Veranstaltungs- und Theaterhaus am Friedrich-Ebert-Platz.', '12', 52.3164848, 10.2326568, 'Friedrich-Ebert-Platz 12', '31226', '#835DC2', '0.125', null, '{"type":"Polygon","coordinates":[[[10.2321534,52.31664],[10.232232,52.3163548],[10.2324099,52.3163732],[10.2324306,52.3162982],[10.2324877,52.3163041],[10.2324955,52.3162756],[10.2329537,52.3163228],[10.2329445,52.3163563],[10.233141,52.3163775],[10.2331317,52.3164201],[10.233099,52.3165691],[10.2330797,52.3166481],[10.2329647,52.3167494],[10.2328947,52.3167361],[10.2329196,52.316646],[10.232737,52.3166273],[10.2327293,52.3166556],[10.2326345,52.3166458],[10.2326533,52.3165776],[10.2323801,52.3165494],[10.2323496,52.3166602],[10.2321534,52.31664]]]}', '10.0'],
			['Schützenplatz Peine', 'Veranstaltungsfläche am Stadtpark.', '13', 52.3217776, 10.2337168, 'Kantstraße', '31224', '#2D946A', '0.150', null, null, '10.0'],
			['Eixer See', 'See und Naherholungsgebiet bei Eixe.', '20', 52.3477, 10.1995, null, '31228', '#2D946A', '0.350', null, '{"type":"Polygon","coordinates":[[[10.2033571,52.3452527],[10.2064862,52.3451642],[10.2068903,52.3470813],[10.1997307,52.3504636],[10.1970968,52.3501875],[10.1945916,52.3497016],[10.1932291,52.3479959],[10.1957074,52.3456805],[10.2002511,52.3454404],[10.2033571,52.3452527]]]}', '2.0'],
		];

		$image = $icons[3];
		foreach ($landmarks as $index => [$name, $info, $category, $lat, $lng, $street, $zip, $color, $radius, $www, $polygon, $view])
		{
			$id = 1041 + $index;
			$address = LocationRegistry::seedAddress((string)$id, [
								'address_name' => $name,
				'address_street' => $street,
				'address_zip' => $zip,
				'address_city' => 'Peine',
				'address_country' => 'DE',
			]);

			LUP_Room::blank([
				'room_id' => (string)$id,
				'room_owner' => null,
				'room_name' => $name,
				'room_info' => $info,
				'room_color' => $color,
				'room_category' => $category,
				'room_pos_lat' => (string)$lat,
				'room_pos_lng' => (string)$lng,
				'room_view' => $view,
				'room_polygon' => $polygon,
				'room_radius' => $radius,
				'room_www' => $www,
				'room_address' => $address->getID(),
				'room_icon' => $image->getID(),
				'room_image' => $image->getID(),
				'room_show_distance' => '1',
			])->softReplace();
		}
	}

	/**
	 * Public-map restaurant snapshot for Peine and its directly connected city
	 * districts. Re-running the installer keeps this list idempotent.
	 */
	private static function seedRestaurants(array $icons): void
	{
		$restaurants = [
			['Mephisto', 52.32472229, 10.23084545, 'Hagenstraße 26', '31224', '{"type":"Polygon","coordinates":[[[10.2306643,52.3247023],[10.2309518,52.3245433],[10.2311396,52.3246328],[10.2312201,52.3248076],[10.2309083,52.3249141],[10.2306643,52.3247023]]]}', '0.15000001'],
			['Zur Hollandsmühle', 52.30614853, 10.22713852, 'Hollandsmühle 4', '31226', '{"type":"Polygon","coordinates":[[[10.2270261,52.3060596],[10.227185,52.3060268],[10.2273654,52.306053],[10.2272769,52.30629],[10.2270623,52.3063281],[10.2268692,52.306281],[10.2270261,52.3060596]]]}', '1.21910477'],
			['Cafe Couture', 52.3229599, 10.22596741, 'Am Markt 22', '31224', '{"type":"Polygon","coordinates":[[[10.2258267,52.322881],[10.2261625,52.322858],[10.226251,52.323023],[10.2261947,52.3231059],[10.2257731,52.3231026],[10.225706,52.3229771],[10.2258267,52.322881]]]}', '0.15000001'],
			['Restaurant No. 90', 52.32302856, 10.22585678, 'Am Markt 22/23', '31224', '{"type":"Polygon","coordinates":[[[10.2259351,52.3229679],[10.2259491,52.3230268],[10.2258329,52.32303],[10.2258394,52.3229705],[10.2259351,52.3229679]]]}', '0.13540031'],
			['La Bruschetta', 52.3225532, 10.2263507, 'Breite Straße 6', '31224', '{"type":"Polygon","coordinates":[[[10.2263129,52.3225545],[10.2264223,52.3224863],[10.2265943,52.3225901],[10.226541,52.3226224],[10.2265293,52.3226156],[10.2264862,52.3226429],[10.2264457,52.3226687],[10.2264056,52.3226452],[10.2264352,52.3226264],[10.2263129,52.3225545]]]}', '0.15000001'],
			['Taormina', 52.3242585, 10.2269908, null, '31224', '{"type":"Polygon","coordinates":[[[10.226853,52.3243317],[10.2269742,52.3242017],[10.2271682,52.3242856],[10.2270443,52.3243992],[10.226853,52.3243317]]]}', '10.0'],
			['Sushi Restaurant Viet Küche', 52.3460083, 10.2414566, 'Peiner Straße 15A', '31228', '{"type":"Polygon","coordinates":[[[10.2414302,52.3460561],[10.2414443,52.345807],[10.2416075,52.3458122],[10.2415953,52.3459651],[10.2416546,52.3459669],[10.2416467,52.3460668],[10.2414302,52.3460561]]]}', '10.0'],
			['Gasthaus zur Sonne', 52.3487371, 10.2432568, 'Edemissener Straße 6', '31228', null, '10.0'],
			['Hemingway', 52.3230840, 10.2260709, null, '31224', null, '10.0'],
			['Center Court', 52.3349421, 10.2211270, null, '31224', null, '10.0'],
			['Härke BrauereiAusschank', 52.3207247, 10.2299440, 'Gröpern 5', '31224', '{"type":"Polygon","coordinates":[[[10.2300029,52.3206648],[10.2298584,52.320787],[10.2300936,52.3208601],[10.2302124,52.3207477],[10.2300029,52.3206648]]]}', '10.0'],
			["Domino's Pizza", 52.3241712, 10.2294775, 'Hagenmarkt 25', '31224', null, '10.0'],
			['Vina', 52.3244842, 10.2312297, 'Senator-Voges-Straße 2', '31224', null, '10.0'],
			['bei Artour', 52.3208501, 10.2285030, 'Wallstraße 13', '31224', '{"type":"Polygon","coordinates":[[[10.2285374,52.3208894],[10.2284384,52.3208632],[10.2285427,52.320717],[10.2286415,52.3207433],[10.2286267,52.3207624],[10.2285374,52.3208894]]]}', '10.0'],
			['Theaterrestaurant Peiner Festsäle', 52.3164306, 10.2323192, 'Friedrich-Ebert-Platz 12', '31226', '{"type":"Polygon","coordinates":[[[10.2321534,52.31664],[10.232232,52.3163548],[10.2324099,52.3163732],[10.2324306,52.3162982],[10.2324877,52.3163041],[10.2324955,52.3162756],[10.2329537,52.3163228],[10.2329445,52.3163563],[10.233141,52.3163775],[10.2331317,52.3164201],[10.233099,52.3165691],[10.2330797,52.3166481],[10.2329647,52.3167494],[10.2328947,52.3167361],[10.2329196,52.316646],[10.232737,52.3166273],[10.2327293,52.3166556],[10.2326345,52.3166458],[10.2326533,52.3165776],[10.2323801,52.3165494],[10.2323496,52.3166602],[10.2321534,52.31664]]]}', '10.0'],
			['Osteria Luce', 52.3490910, 10.2449396, 'Edemissener Straße 12', '31228', null, '10.0'],
			['Belgrad Grill', 52.3202048, 10.2425816, 'Woltorfer Straße 70', '31224', null, '10.0'],
			['Steak & Grillhaus Argentina', 52.3236670, 10.2287590, 'Hagenstraße 17', '31224', '{"type":"Polygon","coordinates":[[[10.2287589,52.3235872],[10.2286181,52.3236732],[10.2286354,52.3236838],[10.2287479,52.3237525],[10.2288815,52.3238322],[10.229019,52.3237604],[10.2287589,52.3235872]]]}', '10.0'],
			['Xin Hua', 52.3221698, 10.2265737, null, '31224', null, '10.0'],
			['Vereinsheim KGV Reitlahe e.V.', 52.3291551, 10.2167244, null, '31224', '{"type":"Polygon","coordinates":[[[10.2165916,52.329174],[10.2165969,52.3292161],[10.2166342,52.3292143],[10.2166367,52.329234],[10.2168572,52.3292237],[10.2168494,52.3291612],[10.2167294,52.3291668],[10.2167181,52.3290762],[10.2166193,52.3290808],[10.2166307,52.3291722],[10.2165916,52.329174]]]}', '10.0'],
			['Bürger-Jäger-Heim', 52.3197188, 10.2327911, 'Beethovenstraße 6', '31224', '{"type":"Polygon","coordinates":[[[10.2329996,52.319576],[10.2325143,52.3196595],[10.2326037,52.3198537],[10.2327132,52.3198616],[10.2330679,52.3198033],[10.2329996,52.319576]]]}', '10.0'],
			['Hotel und Gaststätte Löns Krug Geffers', 52.3151882, 10.2388443, 'Braunschweiger Straße 72', '31226', '{"type":"Polygon","coordinates":[[[10.238812,52.3152949],[10.2389172,52.3152969],[10.2389191,52.3152582],[10.2389982,52.3152597],[10.2390069,52.315083],[10.2388148,52.3150795],[10.238812,52.3151365],[10.2387804,52.3151359],[10.2387771,52.3152028],[10.2386826,52.3152011],[10.2386819,52.3152136],[10.2386817,52.3152357],[10.2387711,52.3152373],[10.2387697,52.3152658],[10.2388134,52.3152666],[10.238812,52.3152949]]]}', '10.0'],
			['Asia Gourmet', 52.3376982, 10.2452099, 'Dieselstraße 8c', '31224', '{"type":"Polygon","coordinates":[[[10.2450469,52.3375111],[10.2449514,52.337842],[10.2453699,52.3378853],[10.2454684,52.3375556],[10.2450469,52.3375111]]]}', '10.0'],
			['Restaurant Peking', 52.3264305, 10.2337393, 'Lessingstraße 5', '31224', null, '10.0'],
			['Gasthaus Zum Sundern', 52.3415836, 10.2171707, 'Sundern 1', '31228', '{"type":"Polygon","coordinates":[[[10.2170308,52.3417377],[10.216956,52.3416522],[10.2170344,52.3416266],[10.2168977,52.3414704],[10.2170234,52.3414294],[10.2171655,52.3415918],[10.2171839,52.3415857],[10.2171993,52.3416034],[10.2173149,52.3415656],[10.2173278,52.3415804],[10.2173828,52.3415625],[10.2174437,52.3416321],[10.2172733,52.3416878],[10.2172533,52.341665],[10.2170308,52.3417377]]]}', '10.0'],
			['Madame Le - Sushi & Panasiatisch', 52.3227907, 10.2261255, 'Breite Straße 3', '31224', '{"type":"Polygon","coordinates":[[[10.2260604,52.3227135],[10.2259903,52.3227594],[10.2260665,52.3228096],[10.2260924,52.3227939],[10.2261841,52.3228503],[10.2262125,52.3228678],[10.2262608,52.3228384],[10.2260604,52.3227135]]]}', '10.0'],
			['Cyrano', 52.3227804, 10.2335304, 'Kantstraße 12', '31224', '{"type":"Polygon","coordinates":[[[10.2333189,52.3227397],[10.2337282,52.3227023],[10.2337419,52.3227582],[10.2336969,52.3227623],[10.2337123,52.3228252],[10.2335566,52.3228395],[10.233348,52.3228585],[10.2333189,52.3227397]]]}', '10.0'],
			['PTG Werksgasthaus', 52.3148052, 10.2413447, 'Gerhard-Lucas-Meyer-Straße 14', '31226', '{"type":"Polygon","coordinates":[[[10.2411649,52.3150168],[10.2411699,52.3147937],[10.2411916,52.3147937],[10.2411916,52.3147688],[10.2411608,52.3147667],[10.2411668,52.3145916],[10.2414532,52.3145953],[10.2414471,52.3147984],[10.2415286,52.3147992],[10.241527,52.3150188],[10.2411649,52.3150168]]]}', '10.0'],
		];

		$image = $icons[3];
		foreach ($restaurants as $index => [$name, $lat, $lng, $street, $zip, $polygon, $view])
		{
			$id = 1013 + $index;
			$address = LocationRegistry::seedAddress((string)$id, [
                'address_name' => $name,
				'address_street' => $street,
				'address_zip' => $zip,
				'address_city' => 'Peine',
				'address_country' => 'DE',
			]);

			LUP_Room::blank([
				'room_id' => (string)$id,
				'room_owner' => null,
				'room_name' => $name,
				'room_info' => 'Restaurant in Peine.',
				'room_color' => '#C9821E',
				'room_category' => '14',
				'room_pos_lat' => (string)$lat,
				'room_pos_lng' => (string)$lng,
				'room_view' => $view,
				'room_polygon' => $polygon,
				'room_radius' => '0.075',
				'room_address' => $address->getID(),
				'room_icon' => $image->getID(),
				'room_image' => $image->getID(),
				'room_show_distance' => '1',
			])->softReplace();
		}
	}

	/**
	 * Publicly listed human-medicine practices in Peine.
	 *
	 * One room represents one public practice location. This intentionally does
	 * not duplicate doctors who share a practice, and does not include dental,
	 * veterinary or therapy-only offices. Coordinates are address pins; the
	 * Polygons stay null until a reviewed building outline is available.
	 */
	private static function seedDoctors(array $icons): void
	{
		$doctors = [
			['Allgemeinmedizin Dr. Alexander Weimann', 'Allgemeinmedizinische Praxis.', 52.3375723, 10.1672425, 'Weißdornstraße 65a', '31228', '05171 2811', null],
			['Allgemeinmedizin Dr. Reinhold-Dünow', 'Allgemeinmedizinische Praxis.', 52.3238999, 10.2279235, 'Werderstraße 28', '31224', '05171 297115', null],
			['Nervenärztliche Gemeinschaftspraxis', 'Herr Dr. med. Hans-Werner Müller-Dethard und Amir Shobeiry', 52.3245897, 10.2338981, 'Kantstraße 40', '31224', '05171 15687', 'https://www.nervenarztpraxis-peine.de/'],
			['Allgemeinmedizin Dr. Liane Stropp', 'Allgemeinmedizinische Praxis.', 52.3431545, 10.2468864, 'Regerstraße 25', '31228', '05171 15429', null, '{"type":"Polygon","coordinates":[[[10.2467701,52.3431033],[10.2467859,52.3432198],[10.2470042,52.3431901],[10.2469885,52.3430736],[10.2467701,52.3431033]]]}'],
			['Allgemeinmedizin Katherine Knabe', 'Allgemeinmedizinische Praxis.', 52.3226616, 10.2297407, 'Bodenstedtstraße 7', '31224', '05171 905709', 'https://www.praxiskatherineknabe.de/'],
			['Hausärzte Peine Wöhnke & Miehe', 'Hausärztliche Gemeinschaftspraxis.', 52.3245897, 10.2338981, 'Kantstraße 40', '31224', '05171 3551', 'https://www.hausaerzte-peine.de/'],
			['MVZ Kardiologie Peine', 'Kardiologie im Ärztezentrum.', 52.3288932, 10.2330939, 'Duttenstedter Straße 11', '31224', '05171 76730', 'https://www.kardiologie-peine.de/'],
			['Anästhesiepraxis Peine', 'Anästhesiologische Praxis im Ärztezentrum.', 52.3288932, 10.2330939, 'Duttenstedter Straße 11', '31224', '05171 588008', 'https://www.aerztezentrum-peine.de/'],
			['Augenärzte Peine', 'Augenärztliche Gemeinschaftspraxis.', 52.3232550, 10.2335819, 'Kantstraße 18', '31224', '05171 15358', 'https://augenaerzte-peine.de/'],
			['Dr. med. Frank Seidling', 'Orthopädie und Unfallchirurgie.', 52.3141167, 10.2103986, 'Werner-Nordmeyer-Straße 37', '31226', '05171 5455356', 'https://www.dr-seidling.de/'],
			['HNO-Praxis Peine', 'Hals-, Nasen- und Ohrenheilkunde sowie Allergologie.', 52.3288932, 10.2330939, 'Duttenstedter Straße 11', '31224', '05171 15239', 'https://www.hno-peine.de/'],
			['HNO-Praxis Dr. Christian Rockel', 'Hals-, Nasen- und Ohrenheilkunde.', 52.3329758, 10.2352724, 'Kastanienallee 1', '31224', '05171 488404', null, '{"type":"Polygon","coordinates":[[[10.2351432,52.3331037],[10.2352507,52.3331117],[10.2352645,52.3331202],[10.2354919,52.3331359],[10.2355255,52.3331286],[10.2355987,52.3331335],[10.2356738,52.3331866],[10.2358418,52.3331057],[10.2358577,52.3331069],[10.2358952,52.3330948],[10.2358735,52.3330658],[10.2358557,52.3330573],[10.2357133,52.3329643],[10.2356263,52.3329969],[10.235567,52.3329945],[10.235393,52.3329764],[10.2354306,52.3327855],[10.2352388,52.3327638],[10.2352052,52.3329752],[10.2351518,52.3329752],[10.2351432,52.3331037]]]}'],
			['Urologie Buse & Kistenbrügge', 'Fachärzte für Urologie im Ärztezentrum.', 52.3290188, 10.2334404, 'Duttenstedter Straße 13', '31224', '05171 50880', 'https://www.urologe-peine.de/'],
			['Allgemeinmedizin Dr. Brigitte Sauer', 'Fachärztin für Allgemeinmedizin.', 52.3418793, 10.1858126, 'Kirchvordener Straße 42', '31228', '05171 587871', null],
			// Kept as an ID placeholder: its services are part of Praxisklinik Peine.
			['Allgemeinmedizin & Phlebologie Wawrzyniak-Schulz', 'Allgemeinmedizin und Phlebologie.', 52.3329758, 10.2352724, 'Kastanienallee 1', '31224', '05171 3004', null],
			['Frauenärztin Dr. Dorothea Marhenke', 'Gynäkologie und Geburtshilfe.', 52.3194927, 10.2307352, 'Bahnhofstraße 5', '31224', '05171 14144', null],
			['Frauenärztin Christine Rückum-Savas', 'Gynäkologie und Geburtshilfe.', 52.3264211, 10.2345083, 'Gunzelinstraße 1', '31224', '05171 71441', 'https://www.frauenaerztin-peine.de/'],
			['Hausarzt Stefan Schmidtke', 'Facharzt für Allgemeinmedizin.', 52.3228995, 10.2268313, 'Echternplatz 2-3', '31224', '05171 582258', 'https://www.hausarzt-schmidtke.de/'],
			['Anästhesie Jens Krupke', 'Facharzt für Anästhesiologie.', 52.2894931, 10.2692026, 'Am Erlenbruch 6', '31226', '05171 989510', null],
			['Augenarzt Walter Diegel', 'Facharzt für Augenheilkunde.', 52.3194927, 10.2307352, 'Bahnhofstraße 5', '31224', '05171 71145', null],
			['Chirurgie Dr. Kevork Kalatas', 'Facharzt für Chirurgie.', 52.3290188, 10.2334404, 'Duttenstedter Straße 13a', '31224', '05171 2948333', null],
			['Dermatologie Dr. Michael D. Lütgemeier', 'Allergologie, Dermatologie und Umweltmedizin.', 52.3217776, 10.2337168, 'Am Schützenplatz 1', '31224', '05171 6822', null],
			['Innere Medizin Irwan Iskandar', 'Facharzt für Innere Medizin.', 52.3222925, 10.2277831, 'St.-Jakobi-Kirchplatz 4', '31224', '05171 6974', null],
			['Psychotherapie Dr. Harald Walter', 'Facharzt für Psychotherapie.', 52.3141167, 10.2103986, 'Werner-Nordmeyer-Straße 37', '31226', null, 'https://haraldwalter.de/'],
			['Frauenarztpraxis Inka Dagmar Groke', 'Frauenheilkunde und Geburtshilfe.', 52.3258149, 10.2337117, 'Schwarzer Weg 1', '31224', '05171 6886', 'https://www.praxis-groke.de/'],
			['Urologie Ellen Leukefeld', 'Facharztpraxis für Urologie.', 52.3225283, 10.2302823, 'Bodenstedtstraße 8', '31224', '05171 5808222', 'https://www.urologie-leukefeld.de/'],
			['Frauenarztpraxis Johannes Neimann', 'Gynäkologie und Geburtshilfe.', 52.3294777, 10.1953436, 'Falkenberger Straße 31a', '31224', '05171 508780', 'https://neimann-gyn.de/'],
			['Gemeinschaftspraxis Ritter & von Müller', 'Allgemeinmedizinische Gemeinschaftspraxis.', 52.3488970, 10.2480383, 'Hesebergweg 1a', '31228', '05171 10440', null],
			['Hausarztpraxis Hauptmann & Herbst', 'Innere Medizin, Allgemeinmedizin und Palliativmedizin.', 52.3227674, 10.2298296, 'Werderstraße 44', '31224', '05171 6601', 'https://www.hauptmann-hausarztpraxis.de/'],
			['Orthopädie Peine Cuntze & Mintrop', 'Orthopädische Gemeinschaftspraxis.', 52.3260626, 10.2296877, 'Sedanstraße 26', '31224', '05171 77500', 'https://www.orthopaediepeine.de/'],
			['Dermatologie Kortenacker & Peters', 'Gemeinschaftspraxis für Dermatologie.', 52.3197001, 10.2301645, 'Bahnhofstraße 24', '31224', '05171 14121', null],
			['Frauenheilkunde Eichler-Pajunk & Müter', 'Gynäkologische Gemeinschaftspraxis.', 52.3197488, 10.2334782, 'Beethovenstraße 9', '31224', '05171 3635', 'https://www.frauenheilkunde-peine.de/'],
			['Gastroenterologie Peine', 'Gastroenterologie und Onkologie im Ärztezentrum.', 52.3290188, 10.2334404, 'Duttenstedter Straße 13', '31224', '05171 54576', 'https://www.gastro-peine.de/'],
			['Kinderarztpraxis Köhler & Weidner', 'Kinder- und Jugendmedizin.', 52.3259204, 10.2277116, 'Bleicherwiesen 13', '31224', '05171 17142', 'https://www.ab-zum-kinderarzt.de/'],
			['Onkologie & Hämatologie Peine', 'Praxis für Onkologie und Hämatologie.', 52.3225283, 10.2302823, 'Bodenstedtstraße 8', '31224', '05171 769600', 'https://www.onkologie-peine.de/'],
			['Ortho Team Peine', 'Orthopädie und Unfallchirurgie.', 52.3194322, 10.2318584, 'Glockenstraße 8', '31224', '05171 40000', 'https://www.orthoteampeine.de/'],
			['Hausarzt Zentrum Peine', 'Hausärztliches Zentrum.', 52.3302952, 10.2459463, 'Eichendorffstraße 15', '31224', '05171 6009', 'https://www.hausarzt-peine.de/'],
			['Hausarztpraxis Dr. Constantin de Curtis', 'Allgemeinmedizinische Praxis.', 52.3352174, 10.1763452, 'Rilkestraße 49', '31228', '05171 90060', null],
			['Hausarztpraxis Dr. Thomas Roy', 'Allgemeinmedizinische Praxis.', 52.3094279, 10.1562317, 'Birkenweg 19', '31226', '05171 580355', 'https://www.praxis-dr-roy.de/'],
			['Hausarztpraxis in der Südstadt', 'Gemeinschaftspraxis für Allgemeinmedizin.', 52.3136533, 10.2355118, 'Feldstraße 20', '31226', '05171 905660', null],
			['Hausarztpraxis Dr. Kahraman', 'Allgemeinmedizinische Praxis.', 52.3318901, 10.1949015, 'Falkenberger Straße 31b', '31228', '05171 4579700', 'https://www.hausarztpraxispeine.de/'],
			['Kinder- und Jugendarztpraxis Simone Fritz', 'Kinder- und Jugendmedizin.', 52.3245443, 10.2341382, 'Am Silberkamp 2a', '31224', '05171 48288', 'https://kinder-jugend-arztpraxis.de/'],
			['Kinderarztpraxis Brigitte Ridder', 'Kinder- und Jugendmedizin.', 52.3319257, 10.2608137, 'Kunzendorfer Straße 10', '31224', '05171 77950', 'https://www.kinderaerzte-im-netz.de/aerzte/peine/ridder/startseite.html', '{"type":"Polygon","coordinates":[[[10.2608843,52.3319947],[10.260936,52.3318933],[10.260743,52.3318566],[10.2606914,52.331958],[10.2608843,52.3319947]]]}'],
			['Kinderarztpraxis Peine Ilsede', 'Kinder- und Jugendmedizin.', 52.3135569, 10.2346434, 'Berliner Ring 4', '31226', '05171 545990', 'https://www.kap-peine-ilsede.de/'],
			['MKG am Marktplatz', 'Mund-, Kiefer- und Gesichtschirurgie.', 52.3233697, 10.2267095, 'Echternplatz 1', '31224', '05171 585996', 'https://www.mkg-peine.de/'],
			['Neurozentrum Peine', 'Neurologie, Psychiatrie und Psychotherapie.', 52.3288932, 10.2330939, 'Duttenstedter Straße 11', '31224', '05171 7909030', 'https://neurozentrum-peine.de/'],
			['Orthopädische Praxis Stephan Quast', 'Orthopädie und Unfallchirurgie.', 52.3194840, 10.2314555, 'Senator-Voges-Straße 3', '31224', '05171 6183', 'https://www.orthopaedische-praxis-peine.de/'],
			['Praxisklinik Peine', 'Gefäßchirurgie, Phlebologie, Chirurgie und Sportmedizin.', 52.3329758, 10.2352724, 'Kastanienallee 1', '31224', '05171 3004', 'https://www.praxisklinik-peine.de/'],
			['Radiologie Zentrum Peine', 'Radiologie.', 52.3258149, 10.2337117, 'Schwarzer Weg 1', '31224', '05171 5833660', 'https://www.peine-radiologie.de/', '{"type":"Polygon","coordinates":[[[10.2333422,52.3257165],[10.2333119,52.325617],[10.2337286,52.3255696],[10.2338331,52.3259134],[10.2336563,52.3259334],[10.233582,52.3256892],[10.2333422,52.3257165]]]}'],
			['Urologie Hagemann & Reese', 'Urologische Gemeinschaftspraxis.', 52.3258149, 10.2337117, 'Schwarzer Weg 1', '31224', '05171 13331', 'https://www.urologie-peine.de/'],
			['Schönheits- und Privatchirurgie Sabine Burkert', 'Privatpraxis für Chirurgie.', 52.3406173, 10.2473531, 'Wilhelm-Rausch-Straße 19', '31228', '05171 5876833', 'https://www.schoenheitschirurgie-burkert.de/'],
			['Pränataldiagnostik & Humangenetik Peine', 'Zentrum für Pränataldiagnostik und Humangenetik.', 52.3205282, 10.2234310, 'Hermann-Ehlers-Straße 9', '31224', '05171 3775', 'https://www.humgenpeine.de/'],
		];

		$image = $icons[3];
		foreach ($doctors as $index => $doctor)
		{
			[$name, $info, $lat, $lng, $street, $zip, $phone, $www] = $doctor;
			$polygon = $doctor[8] ?? null;
			$id = 1100 + $index;
			// Same address and phone as Praxisklinik Peine (#1147): one venue.
			if ($id === 1114)
			{
				continue;
			}
			$address = LocationRegistry::seedAddress((string)$id, [
								'address_name' => $name,
				'address_street' => $street,
				'address_zip' => $zip,
				'address_city' => 'Peine',
				'address_country' => 'DE',
				'address_phone' => $phone,
			]);

			LUP_Room::blank([
				'room_id' => (string)$id,
				'room_owner' => null,
				'room_name' => $name,
				'room_info' => $info,
				'room_color' => '#B64368',
				'room_category' => '21',
				'room_pos_lat' => (string)$lat,
				'room_pos_lng' => (string)$lng,
				'room_view' => '0.050',
				'room_polygon' => $polygon,
				'room_radius' => '0.025',
				'room_www' => $www,
				'room_phone' => $phone,
				'room_address' => $address->getID(),
				'room_icon' => $image->getID(),
				'room_image' => $image->getID(),
				'room_show_distance' => '1',
			])->softReplace();
		}

		// Remove the former standalone seed record on upgrades as well.
		if ($room = LUP_Room::getById('1114'))
		{
			$room->delete();
		}
	}

}
