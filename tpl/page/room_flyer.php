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
		<section class="lup-room-flyer-sheet">
			<header class="lup-room-flyer-head">
                <div class="lup-room-flyer-brand">link<b>uup</b></div>
                <p><?=t('room_flyer_tagline')?></p>
            </header>
            <div class="lup-room-flyer-place">
                <span class="lup-room-flyer-eyebrow"><?=t('room_flyer_here')?></span>
                <h1><?=html($room->getName())?></h1>
            </div>
			<?php if ($room->getInfo()): ?>
				<p class="lup-room-flyer-info"><?=html($room->getInfo())?></p>
			<?php endif; ?>
			<div class="lup-room-flyer-qr">
				<img src="data:image/gif;base64,<?=$qrCode?>" alt="QR-Code für <?=html($room->getName())?>" />
			</div>
			<p class="lup-room-flyer-cta"><?=t('room_flyer_cta')?></p>
			<p class="lup-room-flyer-copy"><?=t('room_flyer_copy')?></p>
            <p class="lup-room-flyer-steps"><?=t('room_flyer_steps')?></p>
			<?php if ($room->getAddress()): ?>
				<div class="lup-room-flyer-address"><?=$room->displayAddress()?></div>
			<?php endif; ?>
		</section>
		<button class="lup-room-flyer-print" type="button" onclick="window.print()"><?=t('room_flyer_print')?></button>
	</main>
</body>
</html>
