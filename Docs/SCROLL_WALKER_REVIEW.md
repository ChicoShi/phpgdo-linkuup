# Scrollbewegung – vereinfachter aktueller Stand

Nach Nutzerfeedback wurde die laufende Figur zurückgenommen: keine Beine, Fußspuren, zwei Umläufe, Uhrwerk-Beschriftung oder angeheftete Kategorie-Stationen mehr. Die Kugel dreht sich insgesamt nur 120 Grad mit dem Scrollen; eine kleine Nadel begleitet den Weg am Rand. Texte und Aktionen bleiben ruhig. Der Weltabschnitt wurde von mobil 560 auf 190 Bildschirmhöhen-Prozent verkürzt; Kategorien haben wieder ihre normale Inhaltshöhe. Die Nadel reagiert mit kürzerer Glättung.

Geprüft: JS-/PHP-Syntax, Diff sowie Chromium bei 390×844 und 1280×900. Keine Läufer-/Fußspur-Elemente im DOM; keine Überlagerung von Kugel und Einladung in diesen Ansichten. Abschließender Desktopdurchgang ohne JS-Fehler, bestehende externe Ladewarnungen. Native Scrollbewegung und Reduced-Motion-Regeln bleiben erhalten.

Offen vor Merge: echter iPhone-/Safari-Test und FPS-Messung. Kein Deployment.

## Stabiler Randmarker und Vektor-Erde
Der Kapitelwechsel und die SVG-Pfadabtastung wurden vollständig aus der Nadelsteuerung entfernt. Eine native sticky Position im dekorativen Seitenlayer ersetzt sämtliche Scroll-Handler und Nachlauf-Frames der Nadel. Die Route bleibt am Rand und springt nicht mehr in die Inhalte. Die Erde verwendet sechs geglättete stilisierte Kontinentpfade statt Hunderter einzelner 3D-Punkte; eine geklippte Vektorfläche und Schattierung vermitteln räumliche Wirkung. Keine maßgebliche geografische Karte.

Chromium-Prüfung: sechs Scrollpositionen vorwärts/rückwärts am Kapitelende, identische Nadelkoordinaten (0 px Positionssprung). Mobile und Desktop visuell geprüft. Reduced Motion blendet den dekorativen Layer aus. Abschließender Lauf ohne JS-Fehler; externe Ladewarnungen bleiben. Kein gemessener FPS-/echter iPhone-Nachweis.
