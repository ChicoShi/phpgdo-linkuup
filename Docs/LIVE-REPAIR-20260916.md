# Live-Darstellung – Reparatur vom 16.09.2026

## Änderungen

- Die Erde liest Atlas-/Zoom-Adressen aus serverseitig erzeugten Modulpfaden. Relative Pfade aus `document.currentScript` zeigten im Live-Bundle unter `/assets/` auf nicht vorhandene Bilder.
- Normale kurze Smartphone-Displays (z. B. 390 × 667) erhalten die zusammenhängende GPS-/Erdanimation. Statische Alternative bleibt für reduzierte Bewegung und sehr niedrige Ansichten bis 480 px erhalten.
- Eine bestehende Erde wechselt weiterhin zwischen Welt- und Straßenszene, auch beim Zurückscrollen; keine zweite Erde.
- Seitenleiste als blaue, kompakte Navigation mit 44-px-Bedienelementen, eigener Scrollfläche und korrekter Begrenzung zum Bildschirm. Fokus/Escape/inert bleiben koordiniert.
- Asset-Revisionsnummern aktualisiert. Enthält außerdem den noch nicht übernommenen Fix gegen die WebSocket-Keepalive-Warnflut.

## Nachweise

- PHP-Syntax der geänderten PHP-Dateien, JavaScript-Syntax und `git diff --check` erfolgreich.
- Browser: 390 × 667, 390 × 844, 1440 × 1000; je sechs Vorwärts-/Rückwärtspositionen. Keine defekten Erdbilder, kein horizontaler Überstand. Dieselbe Erdinstanz wechselt korrekt in die Vordergrundszene.
- Neun Modulskripte minimiert und im Testbrowser unter einem gemeinsamen `/assets/`-Pfad geladen: Erde/GPS/Finale funktionieren, reduzierte Bewegung bleibt statisch, keine JavaScript-Ausnahmen.
- Öffentliche Login-, Registrierungs- und Rechtstextseiten geprüft. Navigation öffnet/schließt, Escape stellt den Fokus wieder her und löst die Seitensperre. Geschützte Verwaltungsseiten leiten ohne Anmeldung zum Login weiter; deren Bearbeitung ist damit nicht vollständig durchgetestet.

## Übergabe

Aktuellen upstream/main vor dem PR integrieren; bestehende Änderungen an Standortverwaltung/Polygonen/Workers erhalten. Regulären Backend-Cache nach dem späteren Deployment erneuern. Keine Live-Änderung oder Datenmigration durch diesen Arbeitsgang.

Grenzen: Chromium mit mobilen Viewports und Touch-Emulation, kein physisches iPhone/Safari. Produktive Google-Maps-Konfiguration und angemeldete Verwaltungsabläufe müssen auf der Zielinstallation separat geprüft werden. Es wird keine vollständige Fehlerfreiheit der gesamten Anwendung behauptet.
