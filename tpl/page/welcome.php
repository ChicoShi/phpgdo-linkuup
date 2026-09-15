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
		<div class="lup-place-scene lup-city-scene" role="img" aria-label="Acht Orte in den LinkUUp-Kategoriefarben, verbunden durch einen gemeinsamen Weg."><svg class="lup-neighbourhood" viewBox="0 0 560 440" aria-hidden="true"><defs><linearGradient id="city-floor" x2="1" y2="1"><stop stop-color="#3e3958"/><stop offset="1" stop-color="#171d2d"/></linearGradient></defs><path d="M20 290 285 140 548 280 285 425Z" fill="url(#city-floor)" stroke="#b6a0d433"/><path d="M20 290 285 425 548 280v12L285 438 20 302Z" fill="#0d1220"/><path class="lup-city-road" d="M103.76 199.4 C166.76 221.4 166.76 247.76 229.76 225.76 C292.76 247.76 292.76 222.76 355.76 200.76 C418.76 222.76 418.76 229.4 481.76 207.4 C481.76 229.4 481.76 407.76 481.76 385.76 C418.76 407.76 418.76 381.4 355.76 359.4 C292.76 381.4 292.76 390.76 229.76 368.76 C166.76 390.76 166.76 399.76 103.76 377.76"/><path class="lup-hero-route" d="M103.76 199.4 C166.76 221.4 166.76 247.76 229.76 225.76 C292.76 247.76 292.76 222.76 355.76 200.76 C418.76 222.76 418.76 229.4 481.76 207.4 C481.76 229.4 481.76 407.76 481.76 385.76 C418.76 407.76 418.76 381.4 355.76 359.4 C292.76 381.4 292.76 390.76 229.76 368.76 C166.76 390.76 166.76 399.76 103.76 377.76"/><circle class="lup-map-runner" cx="85" cy="335" r="0"/><g class="lup-city-building" style="--building-color:#edb47f" transform="translate(48 145) scale(.68)"><g class="lup-building-body" data-door-y="80.0">
 <defs><linearGradient id="shop-0" x2="0" y2="1"><stop stop-color="#edb47f" stop-opacity=".65"/><stop offset="1" stop-color="#edb47f" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="108" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v88l-53 28L0 88Z" fill="url(#shop-0)" stroke="#edb47f" stroke-opacity=".5"/>
 <path d="M55 27v88l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#edb47f"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#edb47f" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#edb47f" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#edb47f" opacity=".7"/>
 <path d="M7 52l40 20v30L7 82Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 48l46 23 6 13L-3 58Z" fill="#edb47f"/>
 <path d="M70 61l23-12v34L70 95Z" fill="#111a2b" stroke="#edb47f"/>
 <path class="lup-shop-welcome" d="M70 61l23-12v34L70 95Z" fill="#edb47f"/>
 <path d="M68 96l28-14 11 6-28 14Z" fill="#edb47f" opacity=".48"/>

 <ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#edb47f"/>
 <text x="52" y="141" text-anchor="middle" fill="#edb47f">Bar</text></g><circle class="lup-city-anchor" data-route="0" cx="82" cy="80" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#e8c39b" transform="translate(174 153) scale(.68)"><g class="lup-building-body" data-door-y="107.0">
 <defs><linearGradient id="shop-1" x2="0" y2="1"><stop stop-color="#e8c39b" stop-opacity=".65"/><stop offset="1" stop-color="#e8c39b" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="135" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v115l-53 28L0 115Z" fill="url(#shop-1)" stroke="#e8c39b" stroke-opacity=".5"/>
 <path d="M55 27v115l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#e8c39b"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#e8c39b" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#e8c39b" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#e8c39b" opacity=".7"/>
 <path d="M7 79l40 20v30L7 109Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 75l46 23 6 13L-3 85Z" fill="#e8c39b"/>
 <path d="M70 88l23-12v34L70 122Z" fill="#111a2b" stroke="#e8c39b"/>
 <path class="lup-shop-welcome" d="M70 88l23-12v34L70 122Z" fill="#e8c39b"/>
 <path d="M68 123l28-14 11 6-28 14Z" fill="#e8c39b" opacity=".48"/>

 <ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#e8c39b"/>
 <text x="52" y="168" text-anchor="middle" fill="#e8c39b">Café</text></g><circle class="lup-city-anchor" data-route="1" cx="82" cy="107" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#e6a4df" transform="translate(300 145) scale(.68)"><g class="lup-building-body" data-door-y="82.0">
 <defs><linearGradient id="shop-2" x2="0" y2="1"><stop stop-color="#e6a4df" stop-opacity=".65"/><stop offset="1" stop-color="#e6a4df" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="110" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v90l-53 28L0 90Z" fill="url(#shop-2)" stroke="#e6a4df" stroke-opacity=".5"/>
 <path d="M55 27v90l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#e6a4df"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#e6a4df" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#e6a4df" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#e6a4df" opacity=".7"/>
 <path d="M7 54l40 20v30L7 84Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 50l46 23 6 13L-3 60Z" fill="#e6a4df"/>
 <path d="M70 63l23-12v34L70 97Z" fill="#111a2b" stroke="#e6a4df"/>
 <path class="lup-shop-welcome" d="M70 63l23-12v34L70 97Z" fill="#e6a4df"/>
 <path d="M68 98l28-14 11 6-28 14Z" fill="#e6a4df" opacity=".48"/>

 <path d="M12 -3v-9" stroke="#e6a4df" stroke-width="6" stroke-linecap="round"/><path d="M27 -3v-15" stroke="#e6a4df" stroke-width="6" stroke-linecap="round"/><path d="M42 -3v-21" stroke="#e6a4df" stroke-width="6" stroke-linecap="round"/><path d="M57 -3v-9" stroke="#e6a4df" stroke-width="6" stroke-linecap="round"/><path d="M72 -3v-15" stroke="#e6a4df" stroke-width="6" stroke-linecap="round"/><ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#e6a4df"/>
 <text x="52" y="143" text-anchor="middle" fill="#e6a4df">Club</text></g><circle class="lup-city-anchor" data-route="2" cx="82" cy="82" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#91bff3" transform="translate(426 153) scale(.68)"><g class="lup-building-body" data-door-y="80.0">
 <defs><linearGradient id="shop-3" x2="0" y2="1"><stop stop-color="#91bff3" stop-opacity=".65"/><stop offset="1" stop-color="#91bff3" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="108" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v88l-53 28L0 88Z" fill="url(#shop-3)" stroke="#91bff3" stroke-opacity=".5"/>
 <path d="M55 27v88l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#91bff3"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#91bff3" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#91bff3" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#91bff3" opacity=".7"/>
 <path d="M7 52l40 20v30L7 82Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 48l46 23 6 13L-3 58Z" fill="#91bff3"/>
 <path d="M70 61l23-12v34L70 95Z" fill="#111a2b" stroke="#91bff3"/>
 <path class="lup-shop-welcome" d="M70 61l23-12v34L70 95Z" fill="#91bff3"/>
 <path d="M68 96l28-14 11 6-28 14Z" fill="#91bff3" opacity=".48"/>

 <path d="M-5-4 53-48 113-4 55 25Z" fill="var(--building-color)" opacity=".9"/><path d="M53-48 55 25 113-4Z" fill="#171b35" opacity=".23"/><ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#91bff3"/>
 <text x="52" y="141" text-anchor="middle" fill="#91bff3">Kultur</text></g><circle class="lup-city-anchor" data-route="3" cx="82" cy="80" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#84d8c9" transform="translate(48 305) scale(.68)"><g class="lup-building-body" data-door-y="107.0">
 <defs><linearGradient id="shop-4" x2="0" y2="1"><stop stop-color="#84d8c9" stop-opacity=".65"/><stop offset="1" stop-color="#84d8c9" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="135" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v115l-53 28L0 115Z" fill="url(#shop-4)" stroke="#84d8c9" stroke-opacity=".5"/>
 <path d="M55 27v115l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#84d8c9"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#84d8c9" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#84d8c9" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#84d8c9" opacity=".7"/>
 <path d="M7 79l40 20v30L7 109Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 75l46 23 6 13L-3 85Z" fill="#84d8c9"/>
 <path d="M70 88l23-12v34L70 122Z" fill="#111a2b" stroke="#84d8c9"/>
 <path class="lup-shop-welcome" d="M70 88l23-12v34L70 122Z" fill="#84d8c9"/>
 <path d="M68 123l28-14 11 6-28 14Z" fill="#84d8c9" opacity=".48"/>

 <path d="M-5-4 53-48 113-4 55 25Z" fill="var(--building-color)" opacity=".9"/><path d="M53-48 55 25 113-4Z" fill="#171b35" opacity=".23"/><ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#84d8c9"/>
 <text x="52" y="168" text-anchor="middle" fill="#84d8c9">Bildung</text></g><circle class="lup-city-anchor" data-route="7" cx="82" cy="107" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#94dda8" transform="translate(174 313) scale(.68)"><g class="lup-building-body" data-door-y="82.0">
 <defs><linearGradient id="shop-5" x2="0" y2="1"><stop stop-color="#94dda8" stop-opacity=".65"/><stop offset="1" stop-color="#94dda8" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="110" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v90l-53 28L0 90Z" fill="url(#shop-5)" stroke="#94dda8" stroke-opacity=".5"/>
 <path d="M55 27v90l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#94dda8"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#94dda8" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#94dda8" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#94dda8" opacity=".7"/>
 <path d="M7 54l40 20v30L7 84Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 50l46 23 6 13L-3 60Z" fill="#94dda8"/>
 <path d="M70 63l23-12v34L70 97Z" fill="#111a2b" stroke="#94dda8"/>
 <path class="lup-shop-welcome" d="M70 63l23-12v34L70 97Z" fill="#94dda8"/>
 <path d="M68 98l28-14 11 6-28 14Z" fill="#94dda8" opacity=".48"/>

 <path d="M-5 0 53-55 114 0 55 27Z" fill="var(--building-color)"/><path d="M53-55 55 27" stroke="#202535" opacity=".4" stroke-width="3"/><ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#94dda8"/>
 <text x="52" y="143" text-anchor="middle" fill="#94dda8">Sport</text></g><circle class="lup-city-anchor" data-route="6" cx="82" cy="82" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#f0a4b9" transform="translate(300 305) scale(.68)"><g class="lup-building-body" data-door-y="80.0">
 <defs><linearGradient id="shop-6" x2="0" y2="1"><stop stop-color="#f0a4b9" stop-opacity=".65"/><stop offset="1" stop-color="#f0a4b9" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="108" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v88l-53 28L0 88Z" fill="url(#shop-6)" stroke="#f0a4b9" stroke-opacity=".5"/>
 <path d="M55 27v88l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#f0a4b9"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#f0a4b9" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#f0a4b9" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#f0a4b9" opacity=".7"/>
 <path d="M7 52l40 20v30L7 82Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 48l46 23 6 13L-3 58Z" fill="#f0a4b9"/>
 <path d="M70 61l23-12v34L70 95Z" fill="#111a2b" stroke="#f0a4b9"/>
 <path class="lup-shop-welcome" d="M70 61l23-12v34L70 95Z" fill="#f0a4b9"/>
 <path d="M68 96l28-14 11 6-28 14Z" fill="#f0a4b9" opacity=".48"/>

 <path d="M29-8h18v-18h15v18h18V7H62v18H47V7H29Z" fill="#f0a4b9"/><ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#f0a4b9"/>
 <text x="52" y="141" text-anchor="middle" fill="#f0a4b9">Gesundheit</text></g><circle class="lup-city-anchor" data-route="5" cx="82" cy="80" r="2" fill="transparent"/></g><g class="lup-city-building" style="--building-color:#a9b6f9" transform="translate(426 313) scale(.68)"><g class="lup-building-body" data-door-y="107.0">
 <defs><linearGradient id="shop-7" x2="0" y2="1"><stop stop-color="#a9b6f9" stop-opacity=".65"/><stop offset="1" stop-color="#a9b6f9" stop-opacity=".22"/></linearGradient></defs>
 <ellipse cx="57" cy="135" rx="72" ry="25" fill="#050a15" opacity=".4"/>
 <path d="M0 0 55 27 108 0v115l-53 28L0 115Z" fill="url(#shop-7)" stroke="#a9b6f9" stroke-opacity=".5"/>
 <path d="M55 27v115l53-27V0Z" fill="#101527" opacity=".65"/>
 <path d="M-4-4 53-32 112-4 55 25Z" fill="#a9b6f9"/>
 <path d="M-4-4v7l59 29 57-28V-4L55 25Z" fill="#a9b6f9" opacity=".55"/>
 <path d="M12-3 53-23 96-3 55 17Z" fill="#20243b" opacity=".35"/>
 <path d="M9 24l36 18v18L9 42Z" fill="#111c30" stroke="#a9b6f9" stroke-opacity=".5"/>
 <path d="M21 30v18m12-12v18" stroke="#a9b6f9" opacity=".7"/>
 <path d="M7 79l40 20v30L7 109Z" fill="#ffdab6" opacity=".32"/>
 <path d="M3 75l46 23 6 13L-3 85Z" fill="#a9b6f9"/>
 <path d="M70 88l23-12v34L70 122Z" fill="#111a2b" stroke="#a9b6f9"/>
 <path class="lup-shop-welcome" d="M70 88l23-12v34L70 122Z" fill="#a9b6f9"/>
 <path d="M68 123l28-14 11 6-28 14Z" fill="#a9b6f9" opacity=".48"/>

 <ellipse class="lup-roof-light" cx="53" cy="0" rx="37" ry="18" fill="#a9b6f9"/>
 <text x="52" y="168" text-anchor="middle" fill="#a9b6f9">Städte</text></g><circle class="lup-city-anchor" data-route="4" cx="82" cy="107" r="2" fill="transparent"/></g></svg><span class="lup-place-caption">Ein Weg. Viele Möglichkeiten.</span></div>
	</section>

	<section id="lup-arrival-journey" class="lup-arrival-journey">
		<div class="lup-journey-stage">
        <header><p>DEINE WELT. DEIN NÄCHSTER SCHRITT.</p><h2>Eine ganze Welt.<br><em>Ein Ort für dich.</em></h2><p class="lup-world-intro">Aus Möglichkeiten wird ein Ziel. Und aus einem Ort wird eine echte Begegnung.</p></header>
        <div class="lup-journey-space">
            <div class="lup-world-halo"></div>
            <div class="lup-world-orbit orbit-a"></div><div class="lup-world-orbit orbit-b"></div>
            <div class="lup-world-carrier" aria-hidden="true">
                <svg class="lup-world-pin" viewBox="0 0 200 270"><defs><linearGradient id="lup-pin-gradient" x2="1" y2="1"><stop stop-color="#b3a0ff"/><stop offset="1" stop-color="#45bbeb"/></linearGradient></defs><path fill="url(#lup-pin-gradient)" d="M100 10C50 10 10 50 10 100c0 68 90 155 90 155s90-87 90-155c0-50-40-90-90-90Z"/><circle cx="100" cy="98" r="39" fill="#171b35"/></svg>
                <div class="lup-world-sphere"><div class="lup-world-rotation"></div><div class="lup-world-shade"></div><div class="lup-world-rim"></div></div>
            </div>
            <a class="lup-world-invitation" href="#lup-arrival-categories" data-lup-scroll><span class="lup-invitation-copy"><small>DEIN NÄCHSTER MOMENT</small><strong>Wohin zieht es dich?</strong><span>Acht Kategorien. Dein nächster Ort.</span></span><i class="fas fa-arrow-down" aria-hidden="true"></i></a>
        </div>
        <ol class="lup-arrival-flow"><li><i class="fas fa-globe-europe"></i><div><h3>Entdecke deine Welt</h3><p>Was gibt es direkt um dich herum?</p></div></li><li><i class="fas fa-shoe-prints"></i><div><h3>Mach den ersten Schritt</h3><p>Finde einen Ort, der zu dir passt.</p></div></li><li><i class="fas fa-map-marker-alt"></i><div><h3>Komm wirklich an</h3><p>Dein Ziel führt ins echte Leben.</p></div></li></ol>
		<a class="lup-arrival-next" href="#lup-arrival-categories" data-lup-scroll><span>Kategorien ansehen</span><i class="fas fa-arrow-down"></i></a>
		</div>
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


 <section class="lup-finale" id="lup-how-it-works" aria-labelledby="lup-finale-heading">
  <div class="lup-finale-stage">
   <header class="lup-finale-heading"><p>VOM PUNKT AUF DER KARTE ZUM MITEINANDER</p><h2 id="lup-finale-heading">Wie funktioniert es?</h2></header>
   <div class="lup-finale-visual" aria-hidden="true">
    <div class="lup-finale-globe"></div>
    <div class="lup-finale-street"><svg viewBox="0 0 800 460">
 <defs><linearGradient id="lup-cafe-wall" x2="0" y2="1"><stop stop-color="#c8b8a2"/><stop offset="1" stop-color="#94867b"/></linearGradient><linearGradient id="lup-cafe-glass" x2="1" y2="1"><stop stop-color="#172b37"/><stop offset="1" stop-color="#435a65"/></linearGradient></defs>
 <rect x="0" y="345" width="800" height="115" rx="15" fill="#252b39"/>
 <path d="M0 406H800" stroke="#9e9cae" stroke-width="2" stroke-dasharray="28 24" opacity=".45"/>
 <path d="M0 345H800" stroke="#918c99" stroke-width="15"/>
 <g opacity=".3" fill="#50596c" stroke="#9fa4b544"><path d="M0 130h150v200H0Z"/><path d="M665 95h135v235H665Z"/><path d="M22 158h40v60H22Zm64 0h40v60H86Zm601-33h35v72h-35Zm56 0h35v72h-35Z" fill="#1a2636"/></g>
 <g class="lup-street-block" data-center-x="410" data-center-y="340">
  <ellipse cx="405" cy="343" rx="260" ry="17" fill="#050c18" opacity=".35"/>
  <path d="M175 94 206 72H642V322l-32 20H175Z" fill="#645e62"/>
  <rect x="175" y="94" width="435" height="245" rx="3" fill="url(#lup-cafe-wall)"/>
  <path d="M165 89H620v15H165Z" fill="#d6c6b0"/><path d="M180 113H606" stroke="#ecdfc933" stroke-width="2"/>
  <rect x="194" y="123" width="398" height="43" rx="3" fill="#263b3d"/>
  <text x="393" y="152" text-anchor="middle" fill="#efe2ca" style="font:500 23px Georgia,serif;letter-spacing:8px">CAFÉ</text>
  <rect x="195" y="183" width="115" height="134" fill="url(#lup-cafe-glass)" stroke="#e2d1b7" stroke-width="5"/>
  <rect x="478" y="183" width="112" height="134" fill="url(#lup-cafe-glass)" stroke="#e2d1b7" stroke-width="5"/>
  <path d="M252 184v132M533 184v132M196 227h112m171 0h110" stroke="#cdbb9e" stroke-width="3"/>
  <rect x="352" y="184" width="80" height="154" fill="#263c44" stroke="#dcc9aa" stroke-width="5"/>
  <path d="M362 196h60v93h-60Z" fill="url(#lup-cafe-glass)"/><path d="M418 278v17" stroke="#d5bb8d" stroke-width="4"/>
  <path d="M185 176H601l13 32H173Z" fill="#465b50"/><path d="M173 208H614v12H173Z" fill="#31483e"/>
  <path d="M215 176l-5 32m51-32-2 32m51-32v32m51-32 2 32m51-32 4 32m51-32 7 32m51-32 8 32m41-32 9 32" stroke="#bfbeaa" stroke-width="16" opacity=".65"/>
  <path d="M332 340h120" stroke="#d2c1aa" stroke-width="8"/>
  <g stroke="#9b8c79" stroke-width="4" fill="#b8a58b"><ellipse cx="270" cy="320" rx="28" ry="7"/><path d="M270 324v20m-18-13-7 17m43-17 7 17"/><ellipse cx="510" cy="320" rx="28" ry="7"/><path d="M510 324v20m-18-13-7 17m43-17 7 17"/></g>
  <g fill="#688e78"><ellipse cx="170" cy="285" rx="17" ry="30"/><ellipse cx="623" cy="285" rx="17" ry="30"/></g><path d="M156 306h28l-4 30h-20Zm453 0h28l-4 30h-20Z" fill="#797485"/>
 </g>
 <path class="lup-finale-route" d="M35 375H300Q340 375 365 350L390 338" fill="none" stroke="#c6b5ee" stroke-width="3" stroke-linecap="round"/>
 <circle class="lup-place-pulse" cx="390" cy="330" r="32" fill="#c6b5ee" fill-opacity=".15"/>
</svg><div class="lup-finale-chat"><span>CHAT AM ORT</span><b>Schön, dass du da bist.</b><small>Ein Gespräch beginnt mit einem Hallo.</small><div><i></i><i></i><i></i></div></div></div>
   </div>
   <div class="lup-finale-copy">
    <article><span>01 / ANKOMMEN</span><h3>Geh hin. Sei wirklich da.</h3><p>Entdecke einen Ort, der zu dir passt, und geh persönlich hin. LinkUUp verbindet Menschen, die sich am selben Ort befinden.</p></article>
    <article><span>02 / CHAT BETRETEN</span><h3>Dein Ort öffnet das Gespräch.</h3><p>Wenn du dich am Ort befindest, kannst du seinen Chatraum betreten. Erst dort kommunizierst du mit den Menschen vor Ort.</p></article>
    <article><span>03 / MENSCHEN VERBINDEN</span><h3>Lauter Club. Leise Bibliothek.</h3><p>Schreib miteinander, wenn Musik Gespräche übertönt oder Ruhe wichtig ist. Freunde und neue Begegnungen: digital ins Gespräch kommen, im echten Leben zusammenfinden.</p></article>
    <article><span>AUSBLICK / ZUKUNFTSIDEE</span><h3>Ein Ort. Noch mehr Möglichkeiten.</h3><p>Perspektivisch könnte auch Bezahlen direkt am Ort dazukommen. Heute beginnt die Idee mit dem Wichtigsten: Menschen wieder miteinander ins Gespräch bringen.</p></article>
   </div>
   <div class="lup-finale-progress" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
  </div>
 </section>
	<section class="lup-arrival-invitation"><p>RAUS AUS DEM FEED</p><h2>Dein nächster Ort<br>wartet schon.</h2><a href="<?=$appURL?>">LinkUUp öffnen <i class="fas fa-arrow-right"></i></a></section>
	<footer class="lup-arrival-footer"><div><p><b class="lup-footer-wordmark">link<span>uup</span></b><small>Zusammen unterwegs.</small></p><a class="lup-back-top" href="#" aria-label="Zurück nach oben"><i class="fas fa-arrow-up"></i></a></div><nav aria-label="Rechtliche Informationen"><a href="<?=href('Register', 'TOS')?>">Nutzungsbedingungen</a><a href="<?=href('Core', 'Privacy')?>">Datenschutz</a><a href="<?=href('Core', 'Impressum')?>">Impressum</a></nav></footer>
</main>
