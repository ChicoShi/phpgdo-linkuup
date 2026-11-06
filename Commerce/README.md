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


## Betreiberabsicherung – Soll/Ist vom 06.11.2026 (Auftrag5818)

Vergleich nur anhand offizieller Dokumentation, keine fremden Konten getestet:
- Square beschränkt Teamrechte auf zugewiesene Locations: https://api.squareup.com/help/ca/en/article/5822-employee-permissions
- Stripe beschreibt wiederholungssichere Mutationen: https://docs.stripe.com/api/idempotent_requests
- Stripe empfiehlt separate Teamzugänge und Zwei-Faktor-Authentifizierung: https://support.stripe.com/questions/two-step-authentication-requirement

| Anforderung | Lokaler Stand | Offen vor produktiver Nutzung |
| --- | --- | --- |
| Locationgebundene Rechte | Aktueller GDO-Owner, keine Rechte aus Browserparametern; Access prüft jede Aktion | Echte Sessionmatrix, bestätigter Betreiber-Onboardingprozess und spätere Mitarbeiterrechte |
| Kontosperre | Nur Member ohne user_deleted; keine eigene user_enabled-Spalte im untersuchten GDO_User | Tatsächliche Logout-/Sperr- und Sessionwiderrufswege durchgehend testen, MFA/erneute Anmeldung für sensible Änderungen |
| Inaktive Location | Neue Konfiguration, Bestellung und Testaufladung gesperrt; bestehende Aufträge weiter abwickelbar | Verhalten der künftigen administrativen Sperrgründe festlegen |
| Nachvollziehbarkeit | Neue Bestellungen/Statuswechsel mit Akteur und UTC-Zeit; gleiche Wiederholung erzeugt keinen zweiten Eintrag | Kein revisionssicheres Auditarchiv; alte Historie wird nicht erfunden |
| Atomare Buchung | PilotStore speichert Auftragszustand, Historie und Testledger unter einem Lock; Fehler verwerfen die ganze Änderung | Produktive DB-Transaktionen, Restore-/Crashprüfung und Providerintegration |
| Wiederholung/Konkurrenz | Acht parallele Bestell-Retries und Lieferungen sowie Lieferung/Storno-Rennen getestet | Echte HTTP-/Sessionkonkurrenz und spätere DB-Tests |

Prüfungen: `php tests/access-policy.php`, `php tests/ordering-domain.php`,
`php tests/test-wallet.php`, `php tests/pilot-concurrency.php`.
Diese Tests beweisen keine echte Anmeldung, Betreiberverifikation oder Echtgeldfähigkeit.
Gekauftes Betreiberpaket darf allein keine fremde Location übernehmen oder deren Rechte gewähren.
Keine gemeinsam geteilten Betreiberpasswörter vorsehen; Delegation braucht eigene Identitäten und Rechte.


## Echter localhost-HTTP-Test (Auftrag5823)

Vier getrennte GDO-Formanmeldungen und zwei markierte QA-Locations:41 Checks bestanden
für Locationrechte, fremde Bestellungen/Guthaben, CSRF/Origin, gefälschte Rollenfelder,
Retry, pausierte Location, bestehende Abwicklung, doppelte Lieferung und gelöschten
Betreiber bei offener Sitzung. Kein QA-Rollenparameter ersetzt diese Anmeldungen.
Die Testkonten werden anschließend deaktiviert; Testpasswörter/Cookies bleiben nur
im Arbeitsspeicher des Testlaufs. Lokale Belege liegen bei Simion unter
research/gastro-auth-5823. WebSocket-App-Login und mobile Gesamtbedienung sind damit
noch nicht abschließend geprüft.

Behobene Integrationsfehler: lokaler /backend-Proxy verwendet Host localhost;
der Adapter akzeptiert diesen nur mit App-Origin oder passendem App-Referer
(weiterhin Loopback erforderlich). CSRF-Token wird ausdrücklich in der Sitzung
persistiert. Fehler werden über GDO Application gesetzt; das Framework normalisiert
manche Ablehnungen auf409. Der private Pilotordner muss dem tatsächlichen lokalen
VHost-Benutzer gehören (Modus0700), nicht pauschal www-data.
Benutzerstatus und Owner werden nach Erwerb des Pilotlocks uncached ausDB gelesen.

Weiterhin konkrete PR-/Produktgrenzen:
- room_minion_bot_control ist im lokalen Room-Modell, aber nicht im lokalen Schema.
  Keine automatische Migration/Fremdänderung durchgeführt; zuständigen Maintainer abstimmen.
- DB-Kontosperre/Room-Deaktivierung und JSON-Commit teilen keine DB-Transaktion.
  Ein Race zwischen frischer Prüfung und Commit bleibt möglich; produktiv nicht freigeben.
- Betreiberverifikation, MFA, Mitarbeiterrollen, vollständige de/en und Provider fehlen.
- Ein Provider muss Händler-Onboarding und Zahlungszustände serverseitig bestätigen;
  ein erfolgreicher Browser-Redirect darf keine Gutschrift auslösen.
  Quelle: https://docs.stripe.com/connect/marketplace
- Rechteprüfung bei jeder Anfrage, standardmäßig verweigern und Ressourcenbezug prüfen:
  https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html
