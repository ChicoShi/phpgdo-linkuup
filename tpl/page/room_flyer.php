<?php
namespace GDO\LinkUUp\tpl\page;

use GDO\Core\Website;
use GDO\Language\Trans;
use GDO\LinkUUp\LUP_Room;

/** @var $room LUP_Room */
/** @var $url string */
/** @var $qrCode string */
?>
<!doctype html>
<html lang="<?=Trans::$ISO?>">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?=html(t('room_flyer') . ': ' . $room->getName())?></title>
	<?=Website::displayHead()?>
	<?=Website::displayMeta()?>
	<?=Website::displayLink()?>
</head>
<body class="lup-room-flyer-page">
	<main class="lup-room-flyer">
		<div class="lup-room-flyer-page">
			<?php for ($copy = 0; $copy < 2; $copy++): ?>
				<section class="lup-room-flyer-sheet">
					<div class="lup-room-flyer-brand">LINK<b>UUP</b> · <?=t('room_flyer_brand')?></div>
					<h1><?=html($room->getName())?></h1>
					<?php if ($room->getInfo()): ?>
						<p class="lup-room-flyer-info"><?=html($room->getInfo())?></p>
					<?php endif; ?>
					<div class="lup-room-flyer-qr">
						<img src="data:image/gif;base64,<?=$qrCode?>" alt="QR-Code für <?=html($room->getName())?>" />
					</div>
					<p class="lup-room-flyer-cta"><?=t('room_flyer_cta')?></p>
					<p class="lup-room-flyer-copy"><?=t('room_flyer_copy')?></p>
					<?php if ($room->getAddress()): ?>
						<div class="lup-room-flyer-address"><?=html($room->getAddress()->getAddressLine())?></div>
					<?php endif; ?>
				</section>
			<?php endfor; ?>
		</div>
		<button class="lup-room-flyer-print" type="button" onclick="window.print()">Drucken (A4 · 2× A5)</button>
	</main>
</body>
</html>
