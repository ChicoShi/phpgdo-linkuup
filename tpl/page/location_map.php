<section class="lup-location-map-page">
	<header class="lup-location-map-head">
		<div>
			<small>LINKUUP VERWALTUNG</small>
			<h1>Location-Karte</h1>
			<p>Wähle eine Location aus, passe ihre Fläche direkt auf der Karte an; Änderungen werden automatisch als GeoJSON gespeichert.</p>
		</div>
		<div class="lup-location-map-status" id="lup-location-map-status" aria-live="polite">Karte wird geladen …</div>
	</header>
	<div class="lup-location-map-layout">
		<aside class="lup-location-map-list" aria-label="Locations">
			<label for="lup-location-map-select">Location</label>
			<select id="lup-location-map-select"></select>
		</aside>
		<div class="lup-location-map-canvas-wrap">
			<div id="lup-location-map-canvas" class="lup-location-map-canvas"></div>
			<footer class="lup-location-map-tools">
				<span id="lup-location-map-selected">Keine Location ausgewählt</span>
				<button id="lup-location-map-save" type="button" disabled>Polygon speichern</button>
			</footer>
		</div>
	</div>
</section>
