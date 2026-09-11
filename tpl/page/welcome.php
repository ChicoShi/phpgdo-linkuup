<?php
declare(strict_types=1);

namespace GDO\LinkUUp\tpl\page;

use GDO\LinkUUp\Module_LinkUUp;

$module = Module_LinkUUp::instance();
$appURL = htmlspecialchars($module->cfgAppUrl(), ENT_QUOTES, 'UTF-8');
?>
<script>document.body.classList.add('lup-arrival-active', 'lup-arrival-refresh');</script>
<main class="lup-arrival">
	<div class="lup-arrival-world" aria-hidden="true"><span class="lup-arrival-world-pin"><i class="fas fa-map-marker-alt"></i><b class="lup-arrival-world-ripple ripple-one"></b><b class="lup-arrival-world-ripple ripple-two"></b></span><span class="lup-arrival-world-signal"></span><b class="lup-arrival-world-dot dot-one"></b><b class="lup-arrival-world-dot dot-two"></b><b class="lup-arrival-world-dot dot-three"></b><b class="lup-arrival-world-dot dot-four"></b></div>
	<section class="lup-arrival-hero">
		<div class="lup-arrival-grid" aria-hidden="true"></div><div class="lup-arrival-horizon" aria-hidden="true"></div><div class="lup-arrival-ambient" aria-hidden="true"><i></i></div>
		<div class="lup-arrival-hero-map" aria-hidden="true"><b class="lup-arrival-hero-dot dot-a"></b><b class="lup-arrival-hero-dot dot-b"></b><b class="lup-arrival-hero-dot dot-c"></b></div>
		<div class="lup-arrival-constellation" aria-hidden="true">
			<span class="lup-arrival-orbit orbit-one"></span><span class="lup-arrival-orbit orbit-two"></span>
			<span class="lup-arrival-constellation-core"><i class="fas fa-map-marker-alt"></i></span>
			<span class="lup-arrival-constellation-node node-one"><i class="fas fa-coffee"></i></span>
			<span class="lup-arrival-constellation-node node-two"><i class="fas fa-music"></i></span>
			<span class="lup-arrival-constellation-node node-three"><i class="fas fa-users"></i></span>
			<span class="lup-arrival-route route-one"></span><span class="lup-arrival-route route-two"></span>
			<span class="lup-arrival-float-card card-two"><i></i> LIVE · LOKAL</span>
		</div>
		<div class="lup-arrival-brand"><b>LINKUUP</b><small>BEGEGNUNGEN IN DEINER NÄHE</small></div>
		<div class="lup-arrival-copy"><p class="lup-arrival-kicker"><i class="fas fa-map-marker-alt"></i> DEIN NÄCHSTER MOMENT IST NAH</p><h1>Das Leben findet<br><em>nicht im Feed</em> statt.</h1><p class="lup-intro">Entdecke Orte in deiner Nähe. Finde, was zu dir passt. Und mach aus einem freien Abend eine echte Begegnung.</p><div class="lup-arrival-actions"><a href="<?=$appURL?>"><i class="fas fa-map-marker-alt"></i> LinkUUp öffnen</a><a href="#lup-arrival-journey" data-lup-scroll>So funktioniert’s <i class="fas fa-arrow-down"></i></a></div></div>
		<div class="lup-place-scene" role="img" aria-label="Illustration: Café, Kultur und Musik sind über Wege in einem Viertel verbunden.">
			<div class="lup-place-plane"><span class="lup-place-block block-a"></span><span class="lup-place-block block-b"></span><span class="lup-place-block block-c"></span><span class="lup-place-block block-d"></span><span class="lup-place-road road-a"></span><span class="lup-place-road road-b"></span><span class="lup-place-route"></span><i class="lup-place-signal"></i></div>
			<span class="lup-place-label place-cafe"><i class="fas fa-coffee"></i> Café</span><span class="lup-place-label place-music"><i class="fas fa-music"></i> Musik</span><span class="lup-place-label place-culture"><i class="fas fa-theater-masks"></i> Kultur</span><span class="lup-place-center"><i class="fas fa-map-marker-alt"></i></span><span class="lup-place-caption">Ein Ort. Viele Möglichkeiten.</span>
		</div>
	</section>

	<section id="lup-arrival-journey" class="lup-arrival-journey">
		<header><p>VOM BLICK ZUM MOMENT</p><h2>Weniger suchen.<br>Mehr ankommen.</h2><details class="lup-arrival-more"><summary>Mehr erfahren <i class="fas fa-plus"></i></summary><p>LinkUUp führt nicht in den nächsten Feed, sondern zum nächsten echten Ort – klar, lokal und auf deine Art.</p></details></header>
		<ol class="lup-arrival-flow"><li><i class="fas fa-compass"></i><div><h3>Entdecke</h3><p>Orte in deiner Nähe.</p></div></li><li><i class="fas fa-check"></i><div><h3>Wähle</h3><p>Was jetzt passt.</p></div></li><li><i class="fas fa-walking"></i><div><h3>Geh hin</h3><p>Echt erleben.</p></div></li></ol>
		<a class="lup-arrival-next" href="#lup-arrival-categories" data-lup-scroll><span>Kategorien ansehen</span><i class="fas fa-arrow-down"></i></a>
	</section>

	<section id="lup-arrival-categories" class="lup-arrival-categories">
		<header class="lup-arrival-section-head"><p>ORTE MIT KONTEXT</p><h2>Finde den Ort,<br>der zu dir passt.</h2><details class="lup-arrival-more"><summary>Mehr erfahren <i class="fas fa-plus"></i></summary><p>Die Kategorien zeigen nicht nur einen Namen: Sie machen auf einen Blick klar, welche Art von Begegnung dich dort erwartet.</p></details></header>
		<div class="lup-arrival-category-grid">
			<article class="lup-arrival-category lup-arrival-category-bar"><i class="fas fa-cocktail"></i><span><b>Bar</b><small>Anstoßen. Reden. Bleiben.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-cafe"><i class="fas fa-coffee"></i><span><b>Café</b><small>Kaffee, Gespräche, Pause.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-club"><i class="fas fa-compact-disc"></i><span><b>Club &amp; Disco</b><small>Beat an. Nacht los.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-culture"><i class="fas fa-theater-masks"></i><span><b>Kultur</b><small>Ideen, Bühne, Bücher.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-education"><i class="fas fa-school"></i><span><b>Bildung</b><small>Wissen trifft Menschen.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-sport"><i class="fas fa-futbol"></i><span><b>Sport</b><small>Zusammen aktiv werden.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-health"><i class="fas fa-hospital"></i><span><b>Gesundheit</b><small>Hilfe, wenn sie zählt.</small></span></article>
			<article class="lup-arrival-category lup-arrival-category-city"><i class="fas fa-city"></i><span><b>Städte</b><small>Deine Stadt neu erleben.</small></span></article>
		</div>
		<a class="lup-arrival-next" href="#lup-arrival-principles" data-lup-scroll><span>Weiter zu „Auf deine Art“</span><i class="fas fa-arrow-down"></i></a>
	</section>

	<section id="lup-arrival-principles" class="lup-arrival-principles"><div class="lup-arrival-principles-copy"><p>DEIN RAUM. DEINE ENTSCHEIDUNG.</p><h2>Echte Orte.<br>Klare Kontrolle.</h2></div><div class="lup-arrival-principles-list lup-arrival-principle-flow"><div><i class="fas fa-map-pin"></i><span><b>Vor Ort</b><small>Nur, wo du bist.</small></span></div><div><i class="fas fa-user-shield"></i><span><b>Deine Wahl</b><small>Du bestimmst.</small></span></div><div><i class="fas fa-heart"></i><span><b>Respekt</b><small>Echt. Freiwillig.</small></span></div></div></section>

	<section class="lup-arrival-invitation"><p>RAUS AUS DEM FEED</p><h2>Dein nächster Ort<br>wartet schon.</h2><a href="<?=$appURL?>">LinkUUp öffnen <i class="fas fa-arrow-right"></i></a></section>
	<footer class="lup-arrival-footer"><div><span class="lup-arrival-mark"><i class="fas fa-link"></i></span><p><b>LinkUUp</b><br><small>Echte Begegnungen beginnen in deiner Nähe.</small></p></div><nav aria-label="Rechtliche Informationen"><a href="<?=href('Register', 'TOS')?>">Nutzungsbedingungen</a><a href="<?=href('Core', 'Privacy')?>">Datenschutz</a><a href="<?=href('Core', 'Impressum')?>">Impressum</a></nav></footer>
</main>
