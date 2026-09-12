# Backend-Ansichten – lokaler Review 12.09.2026

Geprüfte angemeldete Zielseiten in Chromium bei 390 × 844: Profil, Konto, Admin/Module, Freunde, Freund hinzufügen, eingehende und gesendete Anfragen, eigene Galerie, Logs, Team-Bereich. Anschließend die Sidebar-Ziele Neuigkeiten, Avatar-Galerie und allgemeine Galerien. Desktop-Sichtprüfung zusätzlich für Freund hinzufügen, Konto und Admin/Module.

Korrigiert:
- Kopfbereich bildet eine eigene Ebene; Kontomenü liegt über Formularen und Suchfeldern.
- Kontomenü lässt sich auch außerhalb und mit Escape schließen. Desktop verwendet denselben Menüknopf.
- Seiten-Untermenüs im normalen Inhaltsfluss, mit lesbaren Links. Freunde-URLs mit Punkt werden erkannt; rohe Prozent-Platzhalter durch verständliche Bezeichnungen ersetzt, aktive Seite markiert. Originalzähler der Listen bleiben erhalten.
- Profilwerte umbrechend statt über den Rand. Breite Tabellen in einer fokussierbaren horizontalen Scrollregion.
- Logs mit umbrechenden Dateinamen und getrennten Such-/Inhaltsbereichen.
- Markdown-Toolbar als horizontal erreichbare einzelne Zeile; deckt nicht mehr den Editor ab. Dunkle Editor-/Vorschauflächen. Formular ohne zusätzlichen inneren Scrollzwang.
- Sidebar ohne alte Kreise und Überlauf, einheitliche Zeilen und aktive Markierung.
- Tastaturfehler beim Öffnen der Sidebar behoben: Fokus erst nach Sichtbarkeit, Tab bleibt im Drawer, Escape schließt.

Verifikation: PHP-/JS-Syntax und Diff-Prüfung; angemeldete visuelle Wiederholungsprüfung nach Layoutkorrekturen. Kontomenü-Ebenen und Wechsel zur Sidebar im echten Browser geprüft. Zusätzlich Playwright: Sidebar sichtbar, kein horizontaler Überlauf, Tab im Drawer, Escape schließt; Kontomenü auf Desktop innerhalb des Viewports und mit Escape geschlossen.

Grenzen: Keine Formulare abgesendet, Freundschaftsanfragen erzeugt, Module umkonfiguriert, Galerien verändert oder Logout ausgelöst. Keine vollständige fachliche End-to-End-Prüfung aller Unterformulare; Safari/iPhone-Hardware nicht geprüft. Bestehende Google-Maps-CORS-/API-Warnungen, ein fehlendes Markdown-Ladegrafik-Asset sowie gemischte Modulübersetzungen sind separate offene Punkte. Screenshots lokal unter /tmp, enthalten Kontodaten und werden nicht eingecheckt. Kein Deployment.
