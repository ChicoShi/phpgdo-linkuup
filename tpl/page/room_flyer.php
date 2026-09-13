<?php
namespace GDO\LinkUUp\tpl\page;

use GDO\LinkUUp\LUP_Room;

/** @var $room LUP_Room */
/** @var $url string */
/** @var $qrCode string */
?>
<main class="lup-room-flyer">
	<section class="lup-room-flyer-sheet">
		<header class="lup-room-flyer-brand">
			<span>LINK</span><b>U</b><span>UP</span>
		</header>
		<div class="lup-room-flyer-accent" style="background-color: <?=html($room->getColor())?>"></div>
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
			<div class="lup-room-flyer-address"><?=$room->displayAddress()?></div>
		<?php endif; ?>
		<p class="lup-room-flyer-url"><?=html($url)?></p>
	</section>
	<button class="lup-room-flyer-print" type="button" onclick="window.print()">Drucken (A5)</button>
</main>
