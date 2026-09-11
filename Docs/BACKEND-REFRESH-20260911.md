# Welcome-Überarbeitung, 11.09.2026

## Zweiter Durchgang: App-Farben und durchgehende Bewegung

Der erste Gestaltungsstand wurde vom Nutzer verworfen. Der aktuelle Stand
orientiert sich an der tatsächlich geöffneten App-Anmeldeseite und deren
Design-Tokens (`#10121d`, `#191c2a`, `#7a5cff`, `#2b9df4`). Die App wurde dabei
nicht verändert. Die nachfolgende Dokumentation des ersten Durchgangs ist
historisch; die rechteckige Ortskarte und die Kategorie-Karten sind ersetzt.

- Neue SVG-Ortslandschaft mit kontinuierlichem Lichtpunkt, schwebender Nadel
  und begrenzter Perspektivbewegung beim Scrollen.
- Seitenlange GPS-Route: Pfad wird aus tatsächlicher Abschnittslage berechnet,
  Nadel folgt dem Lesefortschritt im seitlichen Freiraum. Keine echten GPS-Daten.
- Acht eigens gezeichnete Linienicons statt der Karten. Antippen wählt genau
  eine Kategorie; Beschreibung und Farbakzent wechseln. Semantik: Buttons mit
  `aria-pressed`, normale Tastaturbedienung und `aria-live` für die Beschreibung.
- Gestaffelte Icon-Bewegung beim Eintritt, zusätzliche Hover-/Fokusreaktion,
  ruhende Texte. RAF-Schleife pausiert bei unsichtbarer Seite, unsichtbarer
  Hero-Szene oder reduzierter Bewegung; CSS-Dauerbewegung pausiert offscreen.
- Footer mit LinkUUp-Wortzeichen, frei stehenden Rechtstext-Links und Zurück-nach-
  oben-Aktion; keine Link-Leisten mehr.
- Keine zusätzlichen Pakete, externen Animationsdienste oder gekauften Medien.

Erneut geprüft: PHP-/JS-Syntax und Diff, kein horizontaler Überlauf bei
320/390/414/768/1440 Pixeln. Kategorie-Überläufe bei 320/768 im Test erkannt und
mit 3-/4-spaltiger Anordnung korrigiert; anschließend alle geprüft ohne Überlauf.
Kategorie Café ausgewählt: genau ein aktiver Button, Beschreibung aktualisiert,
korrekter eigener SVG-Farbwert. GPS-Nadel verändert nach echtem Scrollen ihre
Position. Reduced Motion blendet die wandernde Nadel aus und setzt alle vier
geprüften Daueranimationen auf `none`. Nach-oben-Aktion bringt den Anfang auf
79 Pixel unter die Navigation. Keine neue JavaScript-Fehlerart gegenüber den
vorher bereits vorhandenen Theme-/Maps-Meldungen festgestellt.

Aktuelle Screenshots: `output/playwright/linkuup-refresh/v2-*` im Simion-Ordner.
Die unten genannten App-Link-/Theme-Integrationspunkte bleiben offen.

Die bestehende Welcome-Seite erhält eine konsistente mobile Gestaltung, größere
Texte, eine lokale räumliche Ortsillustration, klarere Aktionen und einen ruhigen
Footer. Das Menü startet unabhängig vom gespeicherten Bootstrap-Zustand
geschlossen. Der Cookie-Hinweis und sein vorhandener Bestätigungsweg bleiben
erhalten. Es gibt keine zusätzliche Bibliothek oder externe Grafik.

## Umsetzung

- Scope: `lup-arrival-refresh` auf dem bestehenden Welcome-Template.
- CSS-Karte mit begrenzter Scrollrotation; ausschließlich Transform/Opacity-Motion.
- Kein Scroll-Jacking und kein animierter Fließtext. Reduced Motion schaltet die
  Bewegung ab; außerhalb der sichtbaren Karte pausiert der Lichtpunkt.
- Menü mit inert geschlossenem Inhalt, Fokusführung, Escape, Schließen-Button
  und Außenklick. Das allgemeine Theme behält seine anderen Seiten.
- Gleichmäßige Abschnitte, lesbare Kategorien ohne Ellipsen, mobile Aktionen
  über die volle Breite, mindestens 44 Pixel hohe Bedienelemente.
- Rechtliche Links ohne dekorative Pfeile; doppelte Datenschutz-/Impressum-Links
  im globalen Footer ausgeblendet. Kontakt und Lizenzen bleiben erreichbar.

## Tatsächlich geprüft

- PHP-Syntax beider geänderter PHP-Dateien, Node-Syntax aller drei betroffenen
  JS-Dateien, `git diff --check`: bestanden.
- Firefox-Automation mit installiertem Playwright-Browser: 320, 390, 414, 768,
  1440 Pixel; kein horizontaler Dokumentüberlauf, Einstiegstext sichtbar,
  Menü beim Laden geschlossen.
- Frischer Browserkontext, 390×844: Cookie-Hinweis vorhanden, Menü geschlossen
  und dessen Inhalt inert. Keine automatische Zustimmung.
- Menü öffnen/schließen bei 390 und 1440 Pixeln; Escape und Fokus-Rückgabe.
- Abschnittssprung zum Ablauf: Überschrift sichtbar bei ungefähr 77/91 Pixeln
  vom oberen Rand, unterhalb der Navigation.
- Alle acht Kategorien bei 390 Pixeln ohne internen horizontalen Überlauf.
- Reduced Motion: Lichtpunkt `animation-name: none`.
- Datenschutz-Link geöffnet, Browser-Zurück zur Welcome-Seite erfolgreich.
- Sichtprüfung: Einstieg, Kategorien, Footer sowie Cookie-Erstaufruf.

Screenshots lokal: `/home/shippi/simion/output/playwright/linkuup-refresh/`.
Ein echtes iPhone/Safari und eine angemeldete Sitzung wurden nicht geprüft.
Es wurde keine Bildrate gemessen und keine 60-FPS-Zusage getroffen.

## Bestehende Einschränkungen und PR-Vorbereitung

Schon vor der Änderung meldete das allgemeine Bootstrap5Theme `$ is not a
function`; zudem bestehen Google-Maps-Lade-/Konfigurationsmeldungen. Diese
seitenübergreifenden Probleme sind nicht Bestandteil dieses Design-Commits.
Nach dem Cache-Leeren erschien vorübergehend ein Authentifizierungshinweis aus
der vorhandenen Anwendung. Er wurde nicht gestalterisch versteckt.

Die lokale App-URL ist ohne Protokoll konfiguriert (`app.lup.localhost`) und wird
vom vorhandenen Template als relativer Link aufgelöst. Die zentrale Konfiguration
wurde nicht geändert; der App-Einstieg ist deshalb nicht als funktionsgeprüft
abgenommen. Vor einer Veröffentlichung muss die App-URL korrekt gesetzt sein.

Ausgangscommit: `0947f44`. Feature-Branch:
`simion/backend-arrival-refresh-20260911`.
Der lokale Ausgangsstand war 83 Commits vor `origin/main`. Für den späteren PR
gezielt diesen Design-Commit auf die vereinbarte Review-Basis übernehmen; nicht
ungeprüft sämtliche älteren lokalen Commits mitsenden. Kein Push, kein Merge,
keine Nachricht an Mira/Gizmore in diesem Durchgang.

Vorgeschlagener PR-Titel: **Refresh the LinkUUp welcome page and mobile navigation**

PR-Beschreibung: Die Welcome-Seite startet mit geschlossenem Menü, damit der
Cookie-Hinweis nicht mit einer offenen Navigation konkurriert. Größere mobile
Texte, eine räumliche Ortskarte und ein einheitlicher Footer verbessern die
Lesbarkeit; reduzierte Bewegung und Tastaturbedienung bleiben unterstützt.
Validierung und verbleibende Integrationspunkte stehen oben.


## Dritter Durchgang: gemeinsame Backend-Oberfläche und Scrollkapitel

Die oben beschriebenen Einschränkungen zur App-URL und zum jQuery-Fehler
sind in diesem Durchgang lokal behoben. Die früheren Abschnitte dokumentieren
den damaligen Stand.

- Gemeinsame Navigation mit geschlossenem Startzustand, Sprachauswahl,
  Schließen-Schaltfläche, Hintergrund-Abdeckung und Tastatur-Fokusführung.
  Die alten konkurrierenden Sidebar-/Konto-Layoutcontroller greifen hier nicht mehr.
- Einheitliche App-Farben, Formulare, Buttons und Footer auch außerhalb Welcome.
  Fehlenden DE/EN-Rücklink ergänzt und doppelte Submit-Flächen bereinigt.
- Scrollkapitel: perspektivische Karte klappt mit echtem Scrollfortschritt zusammen;
  drei Ortsmarker ordnen sich zum Ablauf. Kein erzwungenes Scrollen.
  Reduced Motion zeigt den Inhalt statisch ohne lange Sticky-Strecke.
- Lokale App-Konfiguration auf http://app.localhost/ gesetzt und Ziel geöffnet.
  Keine Produktionskonfiguration geändert.
- Bootstrap5Theme separat auf Branch simion/backend-init-fix-20260911:
  installErrorDialog und initSubmitClickAreas erhalten das benötigte jQuery-Argument.
  Dieser Fix gehört als eigene Abhängigkeit zur späteren PR-Integration.

Geprüft in Firefox: Login, Passwort-Rücksetzung, Kontakt, Datenschutz,
Nutzungsbedingungen, Impressum, Welcome. Bei 390 px kein horizontaler Überlauf,
Menü beim Laden geschlossen. Escape schließt und gibt Fokus an sidebarToggle zurück.
Keine pageerror-Ereignisse auf diesen Routen. Gallery/Avatar/News/Lizenzen führen
in dieser Gastsitzung zur Anmeldung; deren geschützte Inhalte sind nicht abgenommen.
Scrollkapitel bei 390 und 1440 px: Sticky-Position 76/90 px, veränderliche
Karten-Transformation, kein horizontaler Überlauf. Reduced Motion entfernt
Scroll-Story-Klasse und Inline-Transformationen. Vergleichsbilder liegen unter
output/playwright/linkuup-refresh/v3-chapter-* im Simion-Arbeitsverzeichnis.

Offen: Google-Maps-CORS-/Konfigurationsmeldungen bleiben bestehen. Ein vorhandener
Authentifizierungshinweis enthält teilweise einen %s-Platzhalter. Keine Formulare
mit Konto-/Nachrichtenwirkung abgeschickt. Echtes iPhone, angemeldete Unterseiten
und Bildrate nicht geprüft. Keine öffentliche Veröffentlichung oder Nachrichten.
