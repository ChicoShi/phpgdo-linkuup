<?php

use GDO\Form\GDT_Form;

/** @var GDT_Form $form */
?>
<section class="lup-room-composer">
	<header class="lup-room-composer-head">
		<div class="lup-room-composer-mark"><i class="fas fa-map-marked-alt"></i></div>
		<div>
			<small>LINKUUP · ORTSWERKSTATT</small>
			<h1>Neue Location anlegen</h1>
			<p>Name und Titel reichen zum Start. Adresse, Kategorie, Karte und Radius folgen direkt danach.</p>
		</div>
	</header>
	<aside class="lup-room-composer-guide" aria-label="Qualitätscheck">
		<span><i class="fas fa-check"></i> Name und Titel</span>
		<span><i class="fas fa-check"></i> Danach Karte öffnen</span>
		<span><i class="fas fa-check"></i> Fläche präzise setzen</span>
	</aside>
	<div class="lup-room-composer-form">
		<?= $form->render() ?>
	</div>
</section>
