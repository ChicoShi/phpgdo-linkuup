# Isolierter Session-Guard-Versuch – kein Produktionspatch

Dieser Patch wird vom Modul NICHT geladen. Nur auf eine separate Kopie von
GDO_Session.php anwenden; niemals auf den laufenden Core. Mira betreut den
Core-Fix. Ausgangsdatei SHA256:
`46573a342486d954656937111731ebf6703a17ad05f41b4701438e9ae190e0ab`.

`compare.php` benötigt die bereits dokumentierte isolierte MariaDB ohne Netzwerk
und `GASTRO_QA_CORE`. Ohne `GASTRO_QA_SESSION_CANDIDATE` läuft die Fehlerbaseline;
mit dem absoluten Pfad zur gepatchten separaten PHP-Datei wird nur in diesem
Testprozess die Kandidatklasse vor dem Autoload geladen. Der Logout-Unterprozess
übernimmt die Umgebung. Keine protected/config.php wird geladen.

Beobachtet: Baseline stellt dirty sess_data nach Logout wieder her. Kandidat
verweigert dies mit409; sauberes Objekt schreibt nichts. Sechs zusätzliche Checks:
gültige Änderung, Konflikt bei paralleler Änderung, erster Wert bleibt erhalten,
explizites Neuladen/erneutes Speichern, verschachtelte Transaktion abgewiesen,
MyISAM abgewiesen. Ergebnisse charakterisieren ausschließlich diese Testfälle.

Mechanik: Snapshot vor erster markierter Änderung; InnoDB-Rowlock vor save;
Vergleich von Token/User/Daten/IP mit Snapshot; erst dann reguläres GDO-save.
Keine automatische Zusammenführung konkurrierender sess_data, kein stiller Retry.

**Nicht zur Integration freigegeben:** Cache-/Hookzustand nach Commitfehler,
Objektladung/Wiederverwendung außerhalb des Harness, Dirty-/Insert-/Gastpfade,
Login-/Logout-HTTP-Hooks, sichere Konfliktantwort im HTTP-Abschluss, Tokenrotation,
WebSocket-Verbindung/Widerruf, Engine-Migration und weitere DBMS sind offen.
Der Kandidat verwendet absichtlich strenge InnoDB-/Snapshot-Voraussetzungen und
würde bestehende MyISAM-Installationen blockieren. Er ist ein Prüfbeitrag, keine
behauptete fertige Core-Lösung. `production_security_pass` bleibt false.
