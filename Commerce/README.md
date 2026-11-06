# Gastro: lokaler Pilot mit Euro-Testguthaben

Stand 06.11.2026, Feature-Branch `simion/gastro-local-20261106`. Kein Live-Release.

## Jetzt implementiert

`Method/GastroLocal.php` verbindet die lokale App mit der echten GDO-Sitzung und der aktuellen Owner-Zuordnung einer Location. Nur app.localhost und Loopback; Member erforderlich, schreibende Aktionen mit Origin und Session-CSRF. Betreiberaktionen prüfen den Owner serverseitig. Neue Bestellungen/Freigaben sind bei deaktivierter oder gelöschter Location gesperrt. Nicht freigegebene Menüdaten werden Gästen nicht ausgegeben; eigene Bestellsnapshots bleiben zugänglich.

`Ordering.php`: serverseitige Centpreise, Warenkorb, feste Tischzahlen, Freigabe/Pause, idempotente Bestellung, Statuswechsel und bestellbezogene Nachrichten.

`TestWallet.php`: getrenntes Euro-Testkonto je GDO-Nutzer, Cent-genaue Testaufladungen, Reservieren, Abbuchen, Freigeben und Erstatten; Journal und idempotente Wiederholungen. 20,00 EUR Testaufladung entsprechen 20,00 EUR Testguthaben. Bestands-Coins werden nicht gelesen oder verändert. Beim Aufladen höchstens1.000 EUR pro Vorgang und10.000 EUR verfügbares+reserviertes Testguthaben; keine Behauptung gesetzlicher Grenzwerte.

Lokaler Speicher `/var/lib/linkuup-gastro-local/pilot-v2.json`, www-data0700-Verzeichnis,0600-Dateien. Globaler flock und atomarer Rename speichern alle Location-Bestellungen gemeinsam mit dem Ledger. Fehler speichern keine Teilbuchung. Neue Aufträge markieren walletFunded; alte idempotente Aufträge ohne dieses Merkmal werden nicht nachträglich belastet. Vor Einführung war das Verzeichnis leer; alte Einzeldateien werden NICHT automatisch migriert. JSON bleibt ein lokaler Pilot, keine produktive Finanzdatenbank.

UI: dezenter Karte-Zugang im vorhandenen Location-Werkzeugbereich; schließbares Overlay, Karte/Warenkorb/Tisch/Testguthaben/Bestellstatus. Fehlendes Testguthaben führt zur Aufladung und zurück zum Warenkorb. Status wird beim Öffnen/Bestelltab aktualisiert, kein Echtzeit-Push. Ursprüngliche Location-CSS unverändert.

## Prüfweg

`php tests/ordering-domain.php` (45 Assertions), `php tests/test-wallet.php` (40 Assertions).
Lokaler Browser-Prüfserver mit ausdrücklich simulierten Identitäten:
`LUP_WALLET_QA=1 LUP_WALLET_APP=<App>/preview/gastro LUP_WALLET_STATE=<privater-Pfad>.json php -S 127.0.0.1:8768 tools/gastro-wallet-preview/router.php`
Dieser Server ist ausschließlich CLI/Loopback, nicht als öffentliche API verwenden. Er prüft UI+Domain, NICHT echte GDO-Anmeldung. Auf app.localhost ist anonymer Zugriff mit403 geprüft; vollständiger echter Owner-/Gastdurchlauf steht aus.

## Vor Live noch offen

Echte Rollen-/Session-/CSRF-End-to-End-Tests, GDO-Datenbanktabellen/Migration/Transaktionen statt Pilot-JSON, Betreiberberechtigung und Paketverkauf, de/en für gesamte eingebettete Oberfläche (derzeit deutsch), Echtzeitstatus, Anwesenheits-/Tischbestätigung, Steuern/Belege/Kasse und Zahlungsanbieter. CSV-Import gehört nur zur früheren isolierten Vorschau, ist im echten lokalen Adapter nicht angeboten. Keine freie PDF-/Foto-Kartenverarbeitung.

PayPal ist kein bloßer Austausch des Testauflade-Buttons: Aufladung erst nach verifiziertem, dedupliziertem Provider-Ereignis; Lieferung und erfolgreicher Capture als getrennte Zustände, Fehler/Retry/Reconciliation, Händlerzuordnung und Auszahlungen. Testaufladung darf in einer öffentlichen API niemals aktiv sein. Keine Produktionsfreigabe durch diese Tests.

## Rechtliche Vorprüfung – Deutschland, Abruf06.11.2026

Die Benennung als Coin oder Guthaben und ein1:1-Kurs entscheiden nicht über die aufsichtsrechtliche Einordnung. Elektronisch gespeicherter monetärer Wert, gegen Geld ausgegeben und von anderen angenommen, kann E-Geld sein. Ein gemeinsames Guthaben verschiedener Bars braucht daher vor Echtgeldbetrieb eine konkrete Prüfung des Geldflusses und eines geeigneten Zahlungsdienstleistermodells.
- Bundesbank, E-Geld: https://www.bundesbank.de/de/aufgaben/unbarer-zahlungsverkehr/ueberwachung/e-geld-603588 (Seiteninhalt gelesen).
- §2 ZAG: https://www.gesetze-im-internet.de/zag_2018/__2.html (Seiteninhalt gelesen). Begrenztes Netz/Warenspektrum sind Voraussetzungen, kein automatisch passendes Label; Absatz2 regelt die Anzeige oberhalb1Mio EUR in zwölf Monaten. Die technische Dienstleisterausnahme setzt u.a. voraus, nicht in Besitz der Gelder zu gelangen.
Keine Feststellung einer Ausnahme oder Rechtsfreigabe für LinkUUp. Erneut prüfen, sobald Händlervertrag, Gelderverwahrung und Provider feststehen.
