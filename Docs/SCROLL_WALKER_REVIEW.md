# Scrollbewegung – vereinfachter aktueller Stand

Nach Nutzerfeedback wurde die laufende Figur zurückgenommen: keine Beine, Fußspuren, zwei Umläufe, Uhrwerk-Beschriftung oder angeheftete Kategorie-Stationen mehr. Die Kugel dreht sich insgesamt nur 120 Grad mit dem Scrollen; eine kleine Nadel begleitet den Weg am Rand. Texte und Aktionen bleiben ruhig. Der Weltabschnitt wurde von mobil 560 auf 190 Bildschirmhöhen-Prozent verkürzt; Kategorien haben wieder ihre normale Inhaltshöhe. Die Nadel reagiert mit kürzerer Glättung.

Geprüft: JS-/PHP-Syntax, Diff sowie Chromium bei 390×844 und 1280×900. Keine Läufer-/Fußspur-Elemente im DOM; keine Überlagerung von Kugel und Einladung in diesen Ansichten. Abschließender Desktopdurchgang ohne JS-Fehler, bestehende externe Ladewarnungen. Native Scrollbewegung und Reduced-Motion-Regeln bleiben erhalten.

Offen vor Merge: echter iPhone-/Safari-Test und FPS-Messung. Kein Deployment.
