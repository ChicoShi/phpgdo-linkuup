"use strict";

(function () {
	const config = window.LUP_LOCATION_MAP;
	if (!config) { return; }

	const canvas = document.getElementById('lup-location-map-canvas');
	const status = document.getElementById('lup-location-map-status');
	const selectedLabel = document.getElementById('lup-location-map-selected');
	const saveButton = document.getElementById('lup-location-map-save');
	const locationSelect = document.getElementById('lup-location-map-select');
	if (!canvas || !window.google || !google.maps) {
		if (status) { status.textContent = 'Google Maps konnte nicht geladen werden.'; }
		return;
	}

	const map = new google.maps.Map(canvas, {
		center: {lat: 52.264, lng: 10.526},
		zoom: 10,
		mapTypeControl: false,
		streetViewControl: false,
		disableDoubleClickZoom: true,
	});
	const layers = new Map();
	let active = null;
	let saveTimer = null;

	function polygonPath(location) {
		const ring = location.polygon && location.polygon.coordinates && location.polygon.coordinates[0];
		if (!Array.isArray(ring)) { return []; }
		return ring.slice(0, -1).map((point) => ({lat: Number(point[1]), lng: Number(point[0])}));
	}

	function setStatus(message) {
		status.textContent = message;
	}

	function addOption(location) {
		const option = document.createElement('option');
		option.value = String(location.id);
		option.textContent = '#' + location.id + ' – ' + location.name;
		locationSelect.appendChild(option);
	}

	function markDirty() {
		if (!active) { return; }
		active.dirty = true;
		active.revision += 1;
		saveButton.disabled = false;
		setStatus('Polygon geändert – wird gespeichert …');
		clearTimeout(saveTimer);
		saveTimer = setTimeout(() => save(active), 450);
	}

	function polygonBounds(polygon) {
		const polygonBounds = new google.maps.LatLngBounds();
		polygon.getPath().forEach((point) => polygonBounds.extend(point));
		return polygonBounds;
	}

	function nearestSegment(path, point) {
		let nearest = 0;
		let nearestDistance = Infinity;
		const latScale = Math.cos(point.lat() * Math.PI / 180);
		for (let i = 0; i < path.getLength(); i++) {
			const a = path.getAt(i);
			const b = path.getAt((i + 1) % path.getLength());
			const ax = (a.lng() - point.lng()) * latScale;
			const ay = a.lat() - point.lat();
			const bx = (b.lng() - point.lng()) * latScale;
			const by = b.lat() - point.lat();
			const dx = bx - ax;
			const dy = by - ay;
			const divisor = (dx * dx) + (dy * dy);
			const t = divisor ? Math.max(0, Math.min(1, -((ax * dx) + (ay * dy)) / divisor)) : 0;
			const px = ax + (t * dx);
			const py = ay + (t * dy);
			const distance = (px * px) + (py * py);
			if (distance < nearestDistance) {
				nearest = i;
				nearestDistance = distance;
			}
		}
		return nearest;
	}

	function select(id) {
		const layer = layers.get(id);
		if (!layer) { return; }
		if (active && active !== layer) {
			active.polygon.setEditable(false);
			active.polygon.setDraggable(false);
			active.polygon.setMap(null);
			active.marker.setMap(null);
		}
		active = layer;
		active.polygon.setMap(map);
		active.marker.setMap(map);
		saveButton.disabled = !active.dirty;
		active.polygon.setEditable(true);
		// Moving a whole geofence is too easy by accident. Its vertices remain
		// editable, but the polygon itself always stays anchored.
		active.polygon.setDraggable(false);
		selectedLabel.textContent = active.location.name + ' – Eckpunkte ziehen, Doppelklick fügt ein, Rechtsklick entfernt';
		map.fitBounds(polygonBounds(active.polygon));
		locationSelect.value = String(active.location.id);
		setStatus('Bearbeitung aktiv.');
	}

	function geoJSON(layer) {
		const coordinates = layer.polygon.getPath().getArray().map((point) => [
			Number(point.lng().toFixed(7)),
			Number(point.lat().toFixed(7)),
		]);
		coordinates.push(coordinates[0]);
		return {type: 'Polygon', coordinates: [coordinates]};
	}

	async function save(layer) {
		if (!layer || !layer.dirty || layer.saving) { return; }
		const revision = layer.revision;
		const polygon = geoJSON(layer);
		layer.saving = true;
		let saved = false;
		saveButton.disabled = true;
		setStatus('Speichere Polygon …');
		try {
			const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
			const response = await fetch(config.saveUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': token || '',
				},
				body: JSON.stringify({room: layer.location.id+'', polygon: polygon}),
			});
			if (!response.ok) { throw new Error('HTTP ' + response.status); }
			saved = true;
			layer.location.polygon = polygon;
			layer.dirty = layer.revision !== revision;
			if (active === layer) {
				saveButton.disabled = !layer.dirty;
				setStatus(layer.dirty ? 'Weitere Änderung erkannt – wird gespeichert …' : 'Gespeichert.');
			}
		} catch (error) {
			if (active === layer) {
				saveButton.disabled = false;
				setStatus('Speichern fehlgeschlagen: ' + error.message);
			}
		} finally {
			layer.saving = false;
			if (saved && layer.dirty) {
				clearTimeout(saveTimer);
				saveTimer = setTimeout(() => save(layer), 450);
			}
		}
	}

	config.locations.forEach((location) => {
		const path = polygonPath(location);
		if (!path.length) { return; }
		const polygon = new google.maps.Polygon({
			paths: path,
			strokeColor: location.color,
			strokeOpacity: 0.9,
			strokeWeight: 2,
			fillColor: location.color,
			fillOpacity: 0.16,
			map: null,
		});
		const marker = new google.maps.Marker({
			position: {lat: location.lat, lng: location.lng},
			map: null,
			title: location.name,
		});
		const layer = {location, polygon, marker, dirty: false, saving: false, revision: 0};
		layers.set(location.id, layer);
		polygon.addListener('click', () => select(location.id));
		polygon.addListener('dblclick', (event) => {
			if (active !== layer || !event.latLng) { return; }
			// This gesture belongs exclusively to the polygon editor; do not let it
			// reach the underlying Google map (or the browser).
			event.domEvent?.preventDefault();
			event.domEvent?.stopImmediatePropagation?.();
			event.domEvent?.stopPropagation?.();
			event.domEvent && (event.domEvent.cancelBubble = true);
			const path = polygon.getPath();
			path.insertAt(nearestSegment(path, event.latLng) + 1, event.latLng);
			return false;
		});
		polygon.addListener('rightclick', (event) => {
			if (active !== layer || event.vertex === undefined) { return; }
			const path = polygon.getPath();
			if (path.getLength() <= 3) {
				setStatus('Ein Polygon braucht mindestens drei Punkte.');
				return;
			}
			event.domEvent?.preventDefault();
			path.removeAt(event.vertex);
		});
		marker.addListener('click', () => select(location.id));
		polygon.getPath().addListener('set_at', markDirty);
		polygon.getPath().addListener('insert_at', markDirty);
		polygon.getPath().addListener('remove_at', markDirty);
		polygon.addListener('dragend', markDirty);
		addOption(location);
	});

	locationSelect.addEventListener('change', () => select(Number(locationSelect.value)));
	saveButton.addEventListener('click', () => save(active));
	const first = config.locations.find((location) => layers.has(location.id));
	if (first) { select(first.id); }
	else { setStatus('Keine Locations mit Polygon geladen.'); }
})();
