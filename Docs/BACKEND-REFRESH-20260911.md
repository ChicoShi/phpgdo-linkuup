# Welcome-Überarbeitung, 11.09.2026

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
