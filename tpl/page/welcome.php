<?php
declare(strict_types=1);

namespace GDO\LinkUUp\tpl\page;

use GDO\LinkUUp\Module_LinkUUp;

$module = Module_LinkUUp::instance();
$appURL = htmlspecialchars($module->cfgAppUrl(), ENT_QUOTES, 'UTF-8');
?>
<script>document.body.classList.add('lup-arrival-active', 'lup-arrival-refresh');</script>
<main class="lup-arrival">
	<svg class="lup-scroll-map" aria-hidden="true"><path class="lup-scroll-track"/><path class="lup-scroll-light"/></svg><span class="lup-scroll-traveller" aria-hidden="true"><svg viewBox="0 0 24 32"><path d="M12 1C5 1 1 6 1 12c0 8 11 19 11 19s11-11 11-19C23 6 19 1 12 1Z"/><circle cx="12" cy="12" r="4"/></svg></span>
	<section class="lup-arrival-hero">
		<div class="lup-arrival-brand"><b>link<span>uup</span></b><small>BEGEGNUNGEN IN DEINER NÄHE</small></div>
		<div class="lup-arrival-copy"><p class="lup-arrival-kicker"><i class="fas fa-map-marker-alt"></i> DEIN NÄCHSTER MOMENT IST NAH</p><h1>Das Leben findet<br><em>nicht im Feed</em> statt.</h1><p class="lup-intro">Entdecke Orte in deiner Nähe. Finde, was zu dir passt. Und mach aus einem freien Abend eine echte Begegnung.</p><div class="lup-arrival-actions"><a href="<?=$appURL?>"><i class="fas fa-map-marker-alt"></i> LinkUUp öffnen</a><a href="#lup-arrival-journey" data-lup-scroll>So funktioniert’s <i class="fas fa-arrow-down"></i></a></div></div>
		<div class="lup-place-scene" role="img" aria-label="Illustration von Orten, die durch eine leuchtende Route verbunden sind.">
			<svg class="lup-neighbourhood" viewBox="0 0 560 480" aria-hidden="true"><defs><radialGradient id="lup-map-glow"><stop stop-color="#765cff" stop-opacity=".22"/><stop offset="1" stop-color="#765cff" stop-opacity="0"/></radialGradient><linearGradient id="lup-route-color"><stop stop-color="#9c85ff"/><stop offset="1" stop-color="#64c7f7"/></linearGradient></defs><circle cx="280" cy="240" r="230" fill="url(#lup-map-glow)"/><g class="lup-map-contours"><path d="M-50 210C80 40 180 410 360 150S570 80 610 240"/><path d="M-50 160C80-10 180 360 360 100S570 30 610 190"/><path d="M-50 260C80 90 180 460 360 200S570 130 610 290"/><path d="M-50 310C80 140 180 510 360 250S570 180 610 340"/><ellipse cx="280" cy="240" rx="230" ry="115"/><ellipse cx="280" cy="240" rx="180" ry="85"/></g><path class="lup-hero-route" d="M85 335C80 230 250 365 280 240S400 80 467 150"/><g class="lup-map-waypoints"><circle cx="85" cy="335" r="7"/><circle cx="280" cy="240" r="9"/><circle cx="467" cy="150" r="7"/></g><circle class="lup-map-runner" r="5" cx="85" cy="335"/></svg>
			<span class="lup-place-label place-cafe"><i class="fas fa-coffee"></i><b>Café</b><small>Zeit für ein Gespräch</small></span><span class="lup-place-label place-music"><i class="fas fa-music"></i><b>Musik</b><small>Zusammen losziehen</small></span><span class="lup-place-center"><i class="fas fa-map-marker-alt"></i></span><span class="lup-place-caption">Draußen beginnt die Verbindung.</span>
		</div>
	</section>

	<section id="lup-arrival-journey" class="lup-arrival-journey">
		<header><p>VOM BLICK ZUM MOMENT</p><h2>Weniger suchen.<br>Mehr ankommen.</h2><details class="lup-arrival-more"><summary>Mehr erfahren <i class="fas fa-plus"></i></summary><p>LinkUUp führt nicht in den nächsten Feed, sondern zum nächsten echten Ort – klar, lokal und auf deine Art.</p></details></header>
		<ol class="lup-arrival-flow"><li><i class="fas fa-compass"></i><div><h3>Entdecke</h3><p>Orte in deiner Nähe.</p></div></li><li><i class="fas fa-check"></i><div><h3>Wähle</h3><p>Was jetzt passt.</p></div></li><li><i class="fas fa-walking"></i><div><h3>Geh hin</h3><p>Echt erleben.</p></div></li></ol>
		<a class="lup-arrival-next" href="#lup-arrival-categories" data-lup-scroll><span>Kategorien ansehen</span><i class="fas fa-arrow-down"></i></a>
	</section>

	<section id="lup-arrival-categories" class="lup-arrival-categories">
		<header class="lup-arrival-section-head"><p>ORTE MIT KONTEXT</p><h2>Finde den Ort,<br>der zu dir passt.</h2><details class="lup-arrival-more"><summary>Mehr erfahren <i class="fas fa-plus"></i></summary><p>Die Kategorien zeigen nicht nur einen Namen: Sie machen auf einen Blick klar, welche Art von Begegnung dich dort erwartet.</p></details></header>
		<div class="lup-destinations" aria-label="Ortskategorien">
		<?php
		$places = [
			['Bar', 'Anstoßen. Reden. Bleiben.', '#edb47f', 'M4 4h16L12 13 4 4Zm8 9v7m-4 0h8M7 7h10'],
			['Café', 'Kaffee, Gespräche und eine kleine Pause.', '#e8c39b', 'M4 9h12v5a6 6 0 0 1-12 0V9Zm12 1h2a3 3 0 0 1 0 6h-2M3 22h15M7 2v3m5-3v3'],
			['Club & Disco', 'Dein Sound. Deine Nacht. Zusammen unterwegs.', '#e6a4df', 'M9 17V5l11-2v12M9 8l11-2M6 21a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm11-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
			['Kultur', 'Neue Perspektiven zwischen Bühne und Ausstellung.', '#91bff3', 'm3 9 9-6 9 6H3Zm2 3v7m5-7v7m4-7v7m5-7v7M2 22h20'],
			['Bildung', 'Neues lernen. Wissen und Ideen teilen.', '#84d8c9', 'M12 5C8 2 4 3 2 4v15c4-2 7-1 10 1 3-2 6-3 10-1V4c-2-1-6-2-10 1Zm0 0v15'],
			['Sport', 'Rauskommen, bewegen und gemeinsam aktiv sein.', '#94dda8', 'm4 8 12 12m-8-16 12 12M2 10l8-8m4 20 8-8M4 12l8-8m0 16 8-8'],
			['Gesundheit', 'Orte für Gesundheit und Wohlbefinden.', '#f0a4b9', 'M8 3h8v5h5v8h-5v5H8v-5H3V8h5V3Z'],
			['Städte', 'Bekannte Wege. Neue Lieblingsorte.', '#a9b6f9', 'M3 21V9h7v12m0-16h7v16m0-9h4v9M1 21h22M5 12h2m-2 4h2m5-8h2m-2 4h2m-2 4h2'],
		];
		foreach ($places as $index => [$name, $description, $color, $path]): ?>
			<button type="button" class="lup-destination<?=$index === 0 ? ' is-selected' : ''?>" style="--place-color:<?=$color?>;--place-index:<?=$index?>" aria-pressed="<?=$index === 0 ? 'true' : 'false'?>" data-description="<?=htmlspecialchars($description, ENT_QUOTES, 'UTF-8')?>"><span class="lup-destination-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?=$path?>"/></svg></span><span><?=$name?></span></button>
		<?php endforeach; ?>
		</div>
		<div class="lup-destination-story" aria-live="polite"><span class="lup-story-orbit" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span><p><small>WORAUF HAST DU LUST?</small><strong>Bar</strong><span>Anstoßen. Reden. Bleiben.</span></p><a href="<?=$appURL?>" aria-label="Orte in LinkUUp entdecken"><i class="fas fa-arrow-right"></i></a></div>
		<a class="lup-arrival-next" href="#lup-arrival-principles" data-lup-scroll><span>Weiter zu „Auf deine Art“</span><i class="fas fa-arrow-down"></i></a>
	</section>

	<section id="lup-arrival-principles" class="lup-arrival-principles"><div class="lup-arrival-principles-copy"><p>DEIN RAUM. DEINE ENTSCHEIDUNG.</p><h2>Echte Orte.<br>Klare Kontrolle.</h2></div><div class="lup-arrival-principles-list lup-arrival-principle-flow"><div><i class="fas fa-map-pin"></i><span><b>Vor Ort</b><small>Nur, wo du bist.</small></span></div><div><i class="fas fa-user-shield"></i><span><b>Deine Wahl</b><small>Du bestimmst.</small></span></div><div><i class="fas fa-heart"></i><span><b>Respekt</b><small>Echt. Freiwillig.</small></span></div></div></section>

	<section class="lup-arrival-invitation"><p>RAUS AUS DEM FEED</p><h2>Dein nächster Ort<br>wartet schon.</h2><a href="<?=$appURL?>">LinkUUp öffnen <i class="fas fa-arrow-right"></i></a></section>
	<footer class="lup-arrival-footer"><div><p><b class="lup-footer-wordmark">link<span>uup</span></b><small>Zusammen unterwegs.</small></p><a class="lup-back-top" href="#" aria-label="Zurück nach oben"><i class="fas fa-arrow-up"></i></a></div><nav aria-label="Rechtliche Informationen"><a href="<?=href('Register', 'TOS')?>">Nutzungsbedingungen</a><a href="<?=href('Core', 'Privacy')?>">Datenschutz</a><a href="<?=href('Core', 'Impressum')?>">Impressum</a></nav></footer>
</main>
