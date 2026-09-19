# Backend-Nachprüfung vom 19.09.2026

Geprüfter Code: 3c4f8e8, Feature-Branch simion/backend-unified-glass-20260919.
PR: https://github.com/gizmore/phpgdo-linkuup/pull/7 (Entwurf).

## Lokale Prüfungen

- Elf Seiten bei 320 und 1440 Pixeln: Konto, Profil, eigene Bestellungen,
  Bestellverwaltung, Adresse hinzufügen, Freund hinzufügen, Galerieliste,
  Teamübersicht, Räume, Kategorien und Statistiken. Alle HTTP 200, keine
  seitlichen Dokumentüberläufe, unerwarteten body-Scrollsperren oder
  JavaScript-Ausnahmen. Dies ist kein vollständiger Funktionstest.
- Gespeicherter Bootstrap-Zustand `gdo|bs5|sidebar-collapsed=1` berücksichtigt.
  Der ursprüngliche Scrollfehler wurde direkt in Shippis Chromium/F12 bei
  390 Pixeln reproduziert und nach der Korrektur bis zum Seitenende getestet.
- Sidebar bei 320 × 568: geöffnet = Hintergrund inert und gesperrt;
  geschlossen = Hintergrund wieder bedienbar und scrollbar.
- Freundschaftsformular nach verzögertem Editorstart: Texteingabe und
  Zeilenumbruch erfolgreich, Formularfeldnamen unverändert. Testtext entfernt.
  Keine Anfrage versendet.
- Bestellfilter enthalten weiterhin elf ursprüngliche Eingabefelder und
  dieselbe Formularaktion. Kein POST und keine Bestellung ausgelöst.
- Galerie-Liste, Ansicht und Bearbeitung bei 320/390/1440: keine Überläufe;
  vorhandene Bilder geladen. Keine Uploads oder Datensatzänderungen.
- PHP-Lint, JavaScript-Syntax und `git diff --check` erfolgreich.

## Grenzen und offene Abnahme

- Bereits vorhandene Google-Maps-Meldungen (API-Key/CORS) und der fehlende
  Markdown-Ladegrafikpfad sind weiterhin sichtbar; nicht als gelöst bewertet.
- Physisches iPhone/Safari, reale Bestellungen/Belege, Checkout-Endpreise,
  tatsächliche Bild-Uploads und serverseitige Formularverarbeitung offen.
- Flyer an Braunschweig als Bildschirm-/A5-Druckvorschau geprüft. QR-Bild
  geladen, aber Scan und lange Raumtexte nicht abschließend geprüft.
- Konflikte mit aktuellem main in Flyer-CSS, Flyer-Template und DE/EN-Dateien
  benötigen eine abgestimmte Auflösung. Kein Main-Merge oder Deployment.
- App-PR #11 und dessen Tests sind getrennt von diesem Backend-Review.

## Ergänzung 20.09.2026 – Sidebar und Layout

- App-nahe Sidebar: blaue Glasflächen, Profilkarte, Wortmarke, dezente Icons und vereinheitlichte Abstände.
- Darstellung auf angemeldeten lokalen Bestellseiten bei 320/390/1440 px geprüft. Interner Scrollbereich bewegt sich; Escape schließt das Menü und hebt die Seitensperre auf.
- Dekorative Ringe überschreiten geometrisch den Drawer; sie werden abgeschnitten und nehmen keine Eingaben entgegen.
- Layoutzuordnung für Neuigkeiten, Datenschutz, Kontakt und Lizenzen korrigiert; Loginfelder separat optisch angeglichen.
- PHP-Lint und Diff-Prüfung bestanden. Keine Käufe, Adressspeicherung oder Live-Änderung.
- Lokaler WebSocket-Protokollfix ist ausdrücklich nicht Teil dieses PR-Updates.
- Der Adresslink-Fix liegt separat im Payment-Modul und ist nicht Bestandteil dieses LinkUUp-PRs.
- Bestehende Flyer-/Sprachkonflikte mit upstream/main bleiben offen. Draft bleibt zur menschlichen Prüfung; kein Merge/Deployment.

## Separater Payment-Patch

`Docs/payment-address-link.patch` enthält den Einzeilenfix für den ungültigen AddAddress-Link in Payment/Method/YourOrders.php. Er wird im Payment-Repository angewendet, nicht im LinkUUp-Modul. Ziel ist Address/Add mit kodiertem Rücksprung auf Payment/YourOrders. Lokal auf Payment-main 0961201 abgeglichen; PHP-Lint und echter Formularaufruf bei 320/1440 px geprüft, keine Adresse gespeichert. Direkter Push nach gizmore/phpgdo-payment war für ChicoShi nicht erlaubt; es existiert daher kein Payment-PR.

Aktueller App-Reviewbranch: https://github.com/ChicoShi/linkuup-app/tree/simion/locations-visual-rail-20260919 . Neuer App-PR muss aus diesem Branch angelegt werden; der ältere PR #11 enthält diese Änderungen nicht. Prüfumfang und neun auch auf main vorhandene Testfehler stehen dort in Docs/APP-REVIEW-20260920.md.
