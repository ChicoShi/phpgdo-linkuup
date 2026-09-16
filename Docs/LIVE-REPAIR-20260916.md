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

## Ergänzung: Durchgehende Schritte und normale CSS-Kaskade

- Alle `!important`-Zusätze aus den neun eigenen Stylesheets entfernt; keine neuen CSS-Dateien oder Imports. Asset-Versionen erneuert.
- Dieselbe GPS-Nadel nutzt eine gemeinsame, scrollabhängige Schrittphase von den Häusern bis zum Finale. Die Beine laufen auch nach der Erde weiter und reagieren beim Zurückscrollen umgekehrt. Im Gebäude sind sie zusammen mit der Nadel verborgen; reduzierte Bewegung bleibt statisch.
- Öffentlichen Endpunkt `Date/Timezones` zur vorhandenen Freigabeliste ergänzt. Dieser enthält nur den Zeitzonenkatalog. Eine fehlende Freigabe erzeugte beim Öffnen der App-Einstellungen als Gast HTTP 307; geschützte Endpunkte bleiben unverändert geschützt.
- Geprüft: PHP-/JS-Syntax, Diff, fünf Wegabschnitte vorwärts/rückwärts, reduzierte Bewegung sowie mobile und Desktop-Ansichten. Eine bestehende Erdinstanz bleibt erhalten; kein horizontaler Überstand und keine defekten Erdbilder.

Lokale Entwicklungsumgebung: vorhandenen Maps-Feature-Branch auf den aktuellen Upstream `c2d5f94` vorgezogen, weil LinkUUp `GDT_Velocity` verwendet. Die im lokalen alten Schema fehlende Tabelle `lup_workers` wurde einmalig aus der bestehenden Moduldefinition angelegt; vorhandene Tabellen und Daten nicht neu installiert. Dies ist keine neue Produktivmigration dieses Patches. Auf der Zielinstallation müssen Modulversionen und vorhandenes Workers-Schema zusammenpassen.

Dieser Ergänzungsstand ist lokal und zur gemeinsamen Durchsicht vorgesehen; kein Deployment durchgeführt.


## Scrollabstimmung nach Feedback – lokaler Stand

- Alte starre Fußspur direkt hinter der Nadel entfernt; genau ein animiertes Fußpaar bleibt. Fußabdrücke auf der Weltkugel bleiben erhalten.
- Große Einladung „Wohin zieht es dich?“ durch kleine Zeile ersetzt, die mit dem Globusfortschritt hochkommt. Drei Schritt-Symbole auch mobil sichtbar und mit derselben vorwärts/rückwärts laufenden Zeitleiste verbunden.
- Kategorieauswahl mit einem ruhigen farbigen Auswahlring, passendem großen Symbol und zugehöriger Erklärung. Automatische Vorschau und bewusste Auswahl verwenden denselben Inhaltsbereich; nach einem Klick bleibt die Auswahl erhalten. Scrollvorschauen lösen keine laufenden Screenreader-Meldungen aus.
- Bar erklärt Ankommen und anschließenden lokalen Chat. „Orte entdecken“ öffnet die vorhandene App; es wird kein automatischer Chatbeitritt oder ungeprüfter Filter-Link eingeführt.

PHP-/JS-Syntax und Diff geprüft. Chromium 390/1440 px: kein horizontaler Überlauf; Kategorieauswahl per Klick geprüft, eine Weltkugel und ein Fußpaar. Reduzierte Bewegung und Rückkehr aus dem Finale geprüft. Bestehende externe Google-Maps/CSP-Meldungen sind von dieser Darstellungsänderung unabhängig. Kein Live-Deployment.
