# Weltkugel und Scrollroute – aktueller Prüfstand

Die Weltkugel zeigt Natural-Earth-Küsten in einer orthografischen Projektion mit Licht und atmosphärischem Rand. Ein vorberechnetes WebP-Atlasbild (64 Ansichten, 529.420 Bytes) ersetzt die groben Kontinentpfade. Zwei Bildausschnitte überblenden benachbarte Frames; der Browser zeichnet keine Geografie neu.

Die kleine GPS-Nadel durchläuft zwei geneigte Umläufe. Vorder-/Hintergrund, Größe und Neigung vermitteln Tiefe. Scrollposition steuert die Bewegung in beide Richtungen mit kurzer Glättung. Im Stillstand läuft keine Animationsschleife. Der native seitliche Marker blendet während der Kugelpassage aus und danach wieder ein. Texte und Aktionen bleiben ruhig; kein Scroll-Jacking.

Geprüft in Chromium: 390 × 844 ohne horizontalen Überlauf, 1280 × 900 visuell, beide Atlasbilder vollständig geladen. Neun Scrollpositionen vorwärts/rückwärts geprüft: identische Transformation an wiederbesuchten Positionen. Reduced Motion blendet die Umläufer aus. Keine JS-Ausnahme während dieses Bewegungsdurchgangs. Bestehende Google-Maps-Warnungen und externe CORS-Ladefehler sind unabhängig davon weiterhin vorhanden.

Offen vor Merge: echtes iPhone/Safari und gemessene Bildrate. Kein Deployment. Quellen und Erzeugung: `www/data/README.md`.

## Durchgehende Nadel und Symbolkontakte

Eine gemeinsame RAF-Steuerung ersetzt die separate Kugel- und Randnadel. Es existiert genau ein sichtbarer Nadelknoten. Er folgt einer sanft geschwungenen Randbahn, nähert sich während der ersten 16 % des Weltkapitels der Kugel, umrundet sie zweimal und kehrt während der letzten 16 % zur Bahn zurück. Dieselbe Scrollposition steuert die Atlasbilder. Hinter der Kugel wird derselbe Knoten verdeckt, nicht durch eine zweite Figur ersetzt.

An der Kategorieanzeige und den drei Prinzipien dockt die Nadel kurz an. Ein transform-/opacity-gesteuerter Lichtring markiert den Kontakt. Kein Kontakt löst eine Aktion aus oder ändert Kategorien. Native vertikale Scrollbewegung bleibt erhalten. Reduced Motion und kurze Viewports blenden den dekorativen Begleiter aus.

Verifikation dieses Stands: eine Nadel, keine separaten Umläufer im DOM. Alle vier Symbolkontakte bei 390 und 1280 px vorwärts/rückwärts angesteuert; jeweils nur das zugehörige Symbol leuchtet. Kontaktpositionen stimmen innerhalb 0,01 px bei Wiederholung überein. Reduced Motion verbirgt die Nadel. Mobile Kontaktansicht und Desktop-Kategorieansicht visuell geprüft; Syntax und Diff bestanden.
