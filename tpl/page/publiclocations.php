<?php
declare(strict_types=1);

use GDO\LinkUUp\LUP_Room;

/** @var LUP_Room|null $room */
/** @var LUP_Room[] $rooms */
/** @var string $appURL */

$renderAddress = static function (LUP_Room $location): string {
	$address = $location->getAddress();
	return $address && !$address->emptyAddress() ? $address->getAddressLine() : '';
};
?>
<main class="lup-public-locations">
	<?php if ($room): ?>
		<article class="lup-public-location">
			<p class="lup-public-location-label"><?=t('link_public_locations')?></p>
			<h1><?=html($room->getName())?></h1>
			<?php if ($info = $room->getInfo()): ?><p><?=nl2br(html($info))?></p><?php endif; ?>
			<?php if ($address = $renderAddress($room)): ?><address><?=html($address)?></address><?php endif; ?>
			<a href="<?=html($appURL . '#!/location/' . $room->getID())?>"><?=t('link_open_linkuup_location')?></a>
		</article>
	<?php else: ?>
		<section>
			<h1><?=t('mt_linkuup_publiclocations')?></h1>
			<?php if (!$rooms): ?>
				<p><?=t('lup_public_locations_empty')?></p>
			<?php else: ?>
				<ul>
					<?php foreach ($rooms as $location): ?>
						<li>
							<a href="<?=html($location->href_public())?>"><?=html($location->getName())?></a>
							<?php if ($address = $renderAddress($location)): ?> — <?=html($address)?><?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
	<?php endif; ?>
</main>
