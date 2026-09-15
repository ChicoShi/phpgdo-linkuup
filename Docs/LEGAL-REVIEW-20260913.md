# Rechtstexte · technischer Abgleich, 13.09.2026

Die sichtbaren deutschen und englischen Seiten wurden mit dem vorhandenen Code verglichen. Das ist keine abschließende rechtliche Freigabe. Die App-Dialoge laden weiterhin diese serverseitigen Dokumente.

## Korrigiert

Die Datenschutzerklärung behauptete, Positionen nur während einer Verbindung zu halten. `LUP_SignupGPS::updateGPS` speichert tatsächlich die jeweils letzte Position pro Konto in der Datenbank; der Cuddle-Ablauf nutzt sie. Schließen der App beendet diese Speicherung nicht automatisch. Deutsche und englische Fassung beschreiben dies jetzt ausdrücklich. Eine automatische Löschfrist wurde durch diese Textänderung nicht implementiert.

Die englische Haftungsregel wurde für einfache Fahrlässigkeit sprachlich an die deutsche Fassung angeglichen. Daraus folgt keine Garantie, dass sämtliche Klauseln im konkreten Geschäftsmodell wirksam sind. Maßstab für AGB sind unter anderem [§ 307 BGB](https://www.gesetze-im-internet.de/bgb/__307.html) und [§ 309 BGB](https://www.gesetze-im-internet.de/bgb/__309.html).

## Vor Veröffentlichung konkret abgleichen

- Tatsächlicher Anbieter, Kontakt, gegebenenfalls Rechtsform/Register/Vertretung und steuerliche Angaben nach [§ 5 DDG](https://www.gesetze-im-internet.de/ddg/__5.html). Bestehende Angaben nicht als neu bestätigt behandeln; keine fiktiven Register-/Umsatzsteuerdaten ergänzen.
- Hosting, E-Mail-Dienst, Auftragsverarbeitung, Empfänger/Drittlandtransfers, Datenarten, Rechtsgrundlagen und konkrete Speicherdauern. Besonders GPS, Chats, Logs und Backups: technisch tatsächliche Löschung und Auskunftswege nachweisen. Die Informationspflichten stehen in Art. 13, die Speicherbegrenzung in Art. 5 [DSGVO](https://eur-lex.europa.eu/legal-content/DE/TXT/?uri=CELEX:32016R0679).
- Optionale Profilangaben wie Religion und sexuelle Orientierung können besondere Datenkategorien nach Art. 9 DSGVO sein. Eine allgemeine Checkbox oder bloße Sichtbarkeitseinstellung ist kein belegter Ersatz für die erforderliche Rechtsgrundlage. Einwilligung, Widerruf und Löschung als tatsächlichen Ablauf prüfen.
- Mindestalter/Elternzustimmung, Moderation, Beschwerden und geänderte Bedingungen müssen zum tatsächlich umgesetzten Produkt passen. Die aktuellen Texte bestätigen weder eine Altersprüfung noch einen vollständigen Moderationsprozess.
- Cookies/Endgerätezugriffe und externe Aufrufe tatsächlich inventarisieren. Die lokale Welcome-Seite lädt den bestehenden Google-Maps-Code auch ohne nutzbaren Schlüssel; damit sind pauschale Aussagen wie „keine Informationen an Dritte“ nicht ausreichend abgesichert. [§ 25 TDDDG](https://www.gesetze-im-internet.de/ttdsg/__25.html) unterscheidet einwilligungsbedürftige Zugriffe und Ausnahmen. Erforderliche Sitzungscookies und optionale Dienste getrennt bewerten.
- Google Places wird nicht durch einen Maps-Link ersetzt. Echtzeitähnlich abgefragte Öffnungszeiten benötigen eine echte API-Anbindung einschließlich Daten-/Nutzungsbedingungen und Kostenkonfiguration; [Google Places Datenfelder](https://developers.google.com/maps/documentation/places/web-service/data-fields) und [Nutzung/Abrechnung](https://developers.google.com/maps/documentation/places/web-service/usage-and-billing).

Die Datenkorrektur entfernt Import-/Prüfhinweise aus dem normalen Beschreibungstext. Die OSM-Quellenzuordnung bleibt erhalten und wird im Ortsprofil separat genannt. Das ist keine Betreiberverifikation.
