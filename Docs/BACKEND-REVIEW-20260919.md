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

## Weitere Ansichten – 20.09.2026

Enthalten sind jetzt der transparente obere Menübalken, kompakte mobile Bestellungen (YourOrders und Orders), eigene Adressen samt funktionierendem Formularlink, Neuigkeiten/Newsletter/Schreibformular sowie die freigegebene Admin-Gestaltung. Die frühere Aussage „Admin bleibt unverändert“ ist damit überholt.

Prüfung auf Chico bei 320/390/1440 px: Bestellungen behalten 11 bzw. 18 Filterfelder, Adressen sechs; keine Dokumentüberläufe. Adressformular geöffnet, nichts gespeichert. Admin: 70 Modulzeilen/7 Felder, 20 sichtbare Benutzerzeilen/10 Felder, fünf Berechtigungszeilen/4 Felder erhalten. Tabellen horizontal scrollbar, keine JS-Ausnahmen. Keine Module geschaltet, Nutzer geändert oder Rechte vergeben. Weitere Admin-Unterformulare haben die Layoutfreigabe, wurden nicht einzeln funktional durchgetestet.

Newsletter-/News-/Write-Prüfung: vorhandene Felder und Disabled-Zustände erhalten, keine Anmeldung oder Veröffentlichung. Der dabei gefundene Editor-Startfehler ist im **separaten Markdown-Modul** behoben. Dazu liegt `Docs/markdown-editor-initialization.patch` bei (lokaler Modulcommit 8968411). Im Markdown-Repository anwenden und separat reviewen; der LinkUUp-Merge allein aktiviert diesen Fix nicht. Mit diesem Fix laden fünf Sprach-Editoren lokal ohne JS-Ausnahme. RSS bleibt ein unveränderter Feed; nur der Zugangsbutton wurde gestaltet.

Bestehende Flyer-/Sprachkonflikte und menschliche Abnahme bleiben vor dem Merge offen. Keine Live-Übernahme.

## Freund hinzufügen – ergänzender Review

Die Ansicht Friends/Request erhält eine gleichmäßige Bereichsnavigation, blaue transparente Formular-/Editorflächen und eine vollständig sichtbare Editor-Werkzeugleiste. Die unnötigen Zeilennummern sind nur auf Friends/Request deaktiviert; der Editor verwendet seine vorhandene CodeMirror-API. Cache-Versionen wurden angepasst. Keine neuen CSS-Dateien, keine neuen !important-Regeln und keine Änderung an Versand oder Berechtigungen.

Lokaler Browsercheck bei 320/390/1440 px: mehrzeilige Texteingabe funktioniert, Werkzeugbuttons werden vertikal nicht abgeschnitten, Textfeld beginnt unter der Werkzeugleiste, keine Zeilennummern und kein horizontaler Dokumentüberlauf. Keine JS-Ausnahmen im Eingabetest. Die fünf Formularnamen frq_friend, frq_message, frq_relation, xsrf und submit bleiben erhalten. Hintergrund abschließend bei 320/1440 px visuell geprüft. Keine Freundschaftsanfrage versendet. Shippi hat die Darstellung bestätigt; technische Übernahme weiterhin durch Mira/Gizmore prüfen.

Separater lokaler Umgebungsbefund: Backend-Dokumente unter app.localhost/backend/ luden root-relative Assets vom falschen Host (404). Chico leitet solche HTML-Dokumentnavigation jetzt zum lokalen Backend-Origin um; API-Abfragen bleiben im Proxy. Diese Apache-Konfiguration ist nicht Teil dieses PRs und darf nicht ungeprüft als Produktionskonfiguration übernommen werden.
