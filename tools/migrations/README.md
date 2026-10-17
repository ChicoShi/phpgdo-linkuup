# Migration 20261017_room_tolerances

Nur nach separater Deployment-Freigabe durch den Betreiber. PR-Merge führt weder Migration noch Import aus.

1. Ziel-Datenbank ausdrücklich auswählen und Sicherung/Restore prüfen. Anwendungsschreiber anhalten; Migration nicht parallel starten (MySQL-DDL ist nicht transaktional).
2. Vorab ausführen:

```sql
SELECT DATABASE();
SELECT column_name, data_type, is_nullable, column_default
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'lup_room'
  AND column_name IN ('room_join_tolerance', 'room_leave_tolerance');
```

Vorhandene Spalten müssen FLOAT, nullable und DEFAULT NULL sein. Bei anderem Schema stoppen, nicht automatisch umtypen. Prüfen, dass lup_room existiert. Bereits vorhandene Werte werden nicht überschrieben.

3. SQL-Datei 20261017_room_tolerances.sql ausführen. Sie ergänzt ausschließlich fehlende Spalten; auch nach Teilabbruch wiederholbar. Anschließend Abfrage aus Schritt 2 wiederholen und beide Spalten prüfen. Bei einem SQL-Fehler stoppen, keine Imports starten.
4. Erst nach erfolgreicher Prüfung kompatibles Backend und App gemeinsam freigeben. Prüfergebnis und Version im betrieblichen Migrationsprotokoll festhalten. Die Migration wurde in diesem Review nicht auf Produktion ausgeführt; ein isolierter DB-Wiederholungstest steht aus.

## Importumfang

Die 569 lokalen Locations bestehen aus 528 früheren Einträgen plus 41 Ergänzungen. import-reviewed.json allein enthält nur drei. Ausschließlich diese fünf geprüften Batches sind Teil dieser Erweiterung:

- `import-reviewed.json`: 3 aktive Einträge
- `second-import.json`: 12 aktive Einträge
- `regional-import.json`: 13 aktive Einträge
- `regional-next-import.json`: 10 aktive Einträge
- `regional-university-club-cafe.json`: 3 aktive Einträge

Keine Wildcard über alle JSON-Dateien: andere Dateien enthalten Recherche, Duplikate oder zurückgestellte Kandidaten. Pro Batch zuerst `php tools/import_national_locations.php --validate-only --input=DATEI`, danach denselben Befehl ohne `--validate-only` für DB-Dry-run. Dubletten und Quellen prüfen. `--apply-local` schreibt ausschließlich in eine lokale DB; keine Remote-/Produktionssperre umgehen. Ein produktiver Import benötigt eigene Betreiberfreigabe und überprüften Ablauf. Gesamtzahl niemals aus Seed-Zahl allein ableiten.
