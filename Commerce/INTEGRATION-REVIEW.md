# Gastro-Integration: lokaler Prüfstand vom 07.11.2026

Die drei lokalen Abschlussprüfungen sind bestanden: Mitgliedsanmeldung über HTTP mit Cookie-Rotation und anschließender echter WebSocket-Authentifizierung; Bestellung zwischen zwei getrennten Browser-Sitzungen; Offline-Schemamigration mit Wiederholung und Restore in getrennten Testdatenbanken.

Das ist ein **Review-Kandidat für einen isolierten Pilot**, keine Produktions- oder Echtgeldfreigabe. App-PR23 und Backend-PR10 gehören zusammen. Die Änderungen an Core, Session, Login und Websocket sind zusätzliche Abhängigkeiten: `integration-patches/` enthält sie zur separaten Maintainer-Prüfung. Das Backend installiert diese Patches nicht selbst.

## Belegte Abläufe

- 41 HTTP-Prüfungen mit echten GDO-Logins: Gast, fremder Gast, Betreiber und fremder Betreiber; Preise/Identität serverseitig, CSRF, fremde Location/Bestellung, gesperrter Betreiber, Deaktivierung und Abwicklung vorhandener Aufträge, Retry und einmalige Buchung.
- Chromium mit zwei getrennten Sitzungen: Tisch9, Artikel750Cent, Reservierung, automatisch sichtbare Betreiberbestellung, Annahme, Zubereitung, Lieferung. Verfügbares simuliertes Guthaben1250→500Cent; danach0Cent reserviert. Kein manuelles Aktualisieren der Bestelltafel nötig.
- Browser mit den tatsächlichen AuthSrvc/WebsocketSrvc/GWS_Message-Dateien und einem kleinen DI-/HTTP-Testadapter: HTTP-Mitgliedsanmeldung, rotierter Cookie, reale TCP-WebSocket-Anmeldung, HTTP-Logout, Ablehnung eines weiterhin offenen Sockets, erneute Anmeldung, Logout über AuthSrvc. Keine Passwortframes über WebSocket. Dies ist kein vollständiger Angular-SPA-Regressionstest.
- 48 isolierte DB-Prüfungen für den Transaktionsstore, 158 Rechte-,50 Domänen-,40 Wallet- und35 dateibasierte Konkurrenzprüfungen. Die dateibasierten Tests sind kein DB-Nachweis.
- 11 App-Tests für Auth-Reihenfolge, Fehler und Verbindungswechsel; Session-/CSRF-Regression mit echten GDO-Objekten und Widerrufsdispatch separat geprüft.
- Offline-Migration zweimal auf synthetischem Altschema ausgeführt; Sessions werden invalidiert, Ledger bleibt gleich. Separater Restore stellt Daten und ursprüngliche Tabellenengine wieder her.

## Installation und Rückweg zur Prüfung

1. In einer isolierten Kopie exakte Modulbasen und Patchmanifest prüfen. Patches zunächst mit `git apply --check` in den jeweils angegebenen Repositories prüfen. Nicht in fremde/main-Branches schreiben.
2. Core, Session, Login, Websocket, Backend und App gemeinsam bereitstellen. HTTP-/WS-Schreiber während Schemaänderungen stoppen. Vollständiges Backup erstellen und Restore in getrenntem Schema prüfen.
3. `Commerce/migrations/session-pilot-offline.sql` ist eine bewusst manuelle Offline-Migration. Sie setzt InnoDB für Session/User/Location, erweitert Token und ergänzt Authgeneration. Alle bestehenden Sessions werden abgemeldet, auch bei Wiederholung. DDL ist nicht transaktional rückrollbar.
4. Pilot-Ledger mit `Commerce/schema.sql` einrichten. Pilot und Testaufladung bleiben standardmäßig aus. Nur isoliert die beiden strikt booleschen Serverflags aktivieren. Optionaler `GDO_LINKUUP_GASTRO_ORIGIN` darf nur unterstützte lokale HTTP-Hosts mit gültigem Port enthalten; Header ersetzen diese Serverfreigabe nicht.
5. HTTP/WS gemeinsam starten, Mitgliedsanmeldung und Widerruf testen. Für Rückkehr alle Schreiber stoppen, zum gesicherten vollständigen Code-/Datenbankstand zurückkehren und Integrität prüfen. Niemals alte Sessiontabellen mit neuem Sessioncode mischen.

## Weiterhin außerhalb der Freigabe

- Keine echte Zahlung, Aufladung, Auszahlung oder Umwandlung bestehender Coins. Pilot-Guthaben bleibt ausdrücklich simuliert.
- Keine öffentliche Proxy-/Netzwerkfreigabe: Loopback allein schützt nicht hinter einem Reverse-Proxy. Default-off-Prüfungen sind kein Test einer realen öffentlichen Proxykonfiguration.
- Kein regulärer vollständiger LinkUUp-Neuinstallationsnachweis: Der vorhandene Installer scheitert in QA an veralteten Seed-Settings. Für die isolierten Laufzeittests wurde nur der QA-Seed übersprungen und synthetische Konten angelegt. Normale vorhandene Installation/Upgrade und Neuinstallation müssen getrennt bewertet werden.
- Registrierung, Gastkonto-Umwandlung, OAuth, kompletter SPA-Start und Mehrgerätebetrieb sind nicht vollständig nachgewiesen. Websocket hält derzeit eine globale Verbindung je Nutzer.
- Antwortpuffer schützt direkte GWS_Message-Antworten. Fremdmodul-Broadcasts und bereits ausgeführte Fachaktionen sind keine gemeinsame globale Transaktion. Kein universeller Core-Sicherheitsnachweis.
- Produktionsmigration, Lasttests, realer Wiederanlauf und unabhängige Maintainer-Abnahme bleiben separat. Keine automatischen Dienste oder Live-Tabellen verändert.
