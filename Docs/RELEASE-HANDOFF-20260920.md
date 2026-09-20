# Gemeinsamer LinkUUp-Reviewstand – 20.09.2026

Ein Übergabepaket, mehrere Git-Repositories. Diese Datei ist die gemeinsame Übersicht; ein einzelner LinkUUp-Modulmerge installiert keine Änderungen anderer Module. Kein Deployment ausgeführt.

## Verbindliche Quellstände

| Teil | Reviewstand | Grundlage / Umfang |
| --- | --- | --- |
| App | ChicoShi/linkuup-app, simion/locations-visual-rail-20260919, e1ef02b | aktuelles upstream/main bbffe02 integriert; sämtliche Chico-Änderungen bis 2d7a9a0 erhalten |
| Backend | dieser PR #7, einschließlich Merge 38e58df und dieser Dokumentation | upstream/main f36136a integriert; alle Designergänzungen erhalten |
| Payment | Docs/payment-address-link.patch | auf aktuellem origin/main 83dac28 anwendbar; richtige Address/Add-Route |
| Markdown | Docs/markdown-editor-initialization.patch | auf origin/main be1e9cb anwendbar; sequenzieller Editorstart plus Cache-Version |
| QRCode | origin/main 56e6585 oder unten beschriebene PHP-Kompatibilitätsvariante | Runtime des Zielservers prüfen |

## Was bereits upstream ist – nicht doppelt zurückbauen

- App-main bbffe02 enthält bereits unseren Stand bis 0432991 einschließlich Safari-Offsetfix und Glas-Sendebutton. Die Ergänzungen danach (HH:mm, Online-Impuls, stabile Tababstände, kürzeres Wischen, Kartenanzahl) bleiben im neuen Review-Diff sichtbar.
- Backend-main enthält bereits ältere PR-7-Inhalte. Die aktuellen Konflikte sind gelöst: Gizmo res neue Script-Einbindung über Module_LinkUUp und seine BackendLayout-Fassung bleiben erhalten; kein altes Script-Tag im Template wieder eingefügt. Cache-Version für die aktuelle Shell aktualisiert.
- Das aktuelle Backend verarbeitet bereits die paginierte Raumliste einschließlich Gesamtzahl. Lokalen Altfix d699efa nicht zusätzlich blind anwenden.

## QR-PHP-Kompatibilität

QRCode-main referenziert php-qrcode mit PHP-8.4+-Anforderung. Chico verwendet eine ältere kompatible Kombination. Für einen Zielserver unter PHP 8.4 liegt deshalb zusätzlich Docs/qrcode-php81-compatibility.patch bei: Bibliotheken v5.0.5/v3.2.1 und passende GIF-Ausgabeoption. Der Patch basiert auf QRCode-main 56e6585 und erhält dessen neue robuste Base64-Auswertung. Auf passender PHP-8.4+-Runtime nicht pauschal downgraden. Nach Anwendung Submodule auf Gitlinks synchronisieren und echten QR-Render prüfen. Keine unbestätigte Produktions-PHP-Version annehmen.

## Prüfung und offene Abnahme

- App: vollständiger lokaler Satz 55 Tests, 46 bestanden, dieselben neun bereits dokumentierten Baseline-Fehler. Gezielt Social/Viewport 11/11 bestanden. Die bisherigen Browserprüfungen stehen in Docs/APP-REVIEW-20260920.md im App-Repository.
- Backend nach Konfliktauflösung: PHP-Lint von Layout/Modul/Template, JS-Syntax und Diff-Prüfung bestanden. Integrierter Stand ist im separaten Worktree; nicht als bereits im Browser vollständig abgenommen ausgeben.
- Payment-/Markdown-Patches auf frischen isolierten Worktrees des jeweiligen aktuellen main angewendet; PHP-/JS-Syntax und Diff-Prüfung bestanden.
- QR-Kompatibilitätspatch: PHP-Syntax geprüft; finaler Render auf tatsächlicher Zielruntime bleibt erforderlich.
- Offene reale Prüfungen: iPhone/Safari-Tastatur, reale Chat-/GPS-Sitzung, Uploads und Checkout/Belege. Keine echten Käufe/Testnachrichten erzeugt.
- Apache-Umleitung app.localhost/backend bleibt eine lokale Chico-Konfiguration, kein Produktionspatch.

## Übergabe

Backend-PR: https://github.com/gizmore/phpgdo-linkuup/pull/7

App-PR-Anlage: https://github.com/gizmore/linkuup-app/compare/main...ChicoShi:linkuup-app:simion/locations-visual-rail-20260919?expand=1

Der App-PR ist noch nicht angelegt: Chico-Browser ist bei GitHub abgemeldet. Mira/Gizmore bitte Draft anlegen oder die angemeldete Sitzung bereitstellen. Erst nach gemeinsamer Sichtprüfung und Freigabe gezielt übernehmen und deployen. Die obigen Patches müssen in ihren jeweiligen Modul-Repositories berücksichtigt werden.
