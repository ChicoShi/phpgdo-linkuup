# Weltkugel und Scrollroute – aktueller Prüfstand

Die Weltkugel zeigt Natural-Earth-Küsten in einer orthografischen Projektion mit Licht und atmosphärischem Rand. Ein vorberechnetes WebP-Atlasbild (64 Ansichten, 529.420 Bytes) ersetzt die groben Kontinentpfade. Zwei Bildausschnitte überblenden benachbarte Frames; der Browser zeichnet keine Geografie neu.

Die kleine GPS-Nadel durchläuft zwei geneigte Umläufe. Vorder-/Hintergrund, Größe und Neigung vermitteln Tiefe. Scrollposition steuert die Bewegung in beide Richtungen mit kurzer Glättung. Im Stillstand läuft keine Animationsschleife. Der native seitliche Marker blendet während der Kugelpassage aus und danach wieder ein. Texte und Aktionen bleiben ruhig; kein Scroll-Jacking.

Geprüft in Chromium: 390 × 844 ohne horizontalen Überlauf, 1280 × 900 visuell, beide Atlasbilder vollständig geladen. Neun Scrollpositionen vorwärts/rückwärts geprüft: identische Transformation an wiederbesuchten Positionen. Reduced Motion blendet die Umläufer aus. Keine JS-Ausnahme während dieses Bewegungsdurchgangs. Bestehende Google-Maps-Warnungen und externe CORS-Ladefehler sind unabhängig davon weiterhin vorhanden.

Offen vor Merge: echtes iPhone/Safari und gemessene Bildrate. Kein Deployment. Quellen und Erzeugung: `www/data/README.md`.
