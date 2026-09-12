# Scroll-Nadel – lokaler Prüfstand

Die gemeinsame Nadel beginnt vor dem Globus, springt auf seine stilisierte Oberfläche und durchläuft zwei Umläufe. Beine und 36 begrenzte Fußspur-Elemente sind aus dem Scrollfortschritt abgeleitet; keine Endlosschleife. Danach führt die Nadel über die drei Schritte und die acht Kategorie-Symbole in die vorhandene Route. Längere native Scrollabschnitte geben mobilen Lesern mehr Zeit. Eine dekorative Beschriftung wechselt an der Kugel; die eigentlichen Texte bleiben ruhig.

Die Kugel ist eine CSS-3D-Illustration. Der Läufer nutzt projizierte Positionen, Größen und Deckkraft für räumliche Wirkung, kein physikalisches 3D-Figurmodell. Die Kategorieauswahl per Klick bleibt eigenständig; Scrollen aktiviert nur die dekorativen Zielmarkierungen.

Geprüft: JavaScript-/PHP-Syntax und Diff. Chromium 390×844 und 1280×900 visuell; Kategorien bleiben angeheftet und bedienbar. Vorwärts .30 → .50 → rückwärts .30: identische Nadelposition und Beinstellung; acht sichtbare Spurstücke an dieser Testposition. Café-Klick setzt aria-pressed und Beschreibung korrekt. Reduced Motion blendet Läufer/Spuren aus und entfernt die angehefteten Kategorien. Im abschließenden Browserdurchgang keine JS-Fehler, bestehende Google-Maps-Ladewarnung.

Behoben während Prüfung: altes zwischengespeichertes Template ohne Kategorien-Wrapper abgefangen, overflow:hidden am Kategorienabschnitt entfernt, doppelte Fußspur-Schicht entfernt, Überlagerung von Kugel/Beschriftung/Einladung korrigiert.

Noch offen: echtes iPhone/Safari, FPS-Messung und vollständige Navigation-/Tastaturabnahme vor Merge. Keine Installation, Datenbankänderung oder Veröffentlichung.
