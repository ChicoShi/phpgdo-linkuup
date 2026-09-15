# Weitere 100 gemischte Locations – lokaler Prüfstand

Quelle: © OpenStreetMap contributors, ODbL 1.0, https://www.openstreetmap.org/copyright
Einzelquellen, Versionen und Zeitstempel pro Ort und Gebäude in locations.json. Quellen aus den vorherigen Abrufen dieser Arbeitssitzung wiederverwendet. Keine Betreiber- oder Öffnungsbestätigung.

## Verteilung

- Braunschweig: 15
- Wolfsburg: 7
- Peine: 1
- Hannover: 46
- Hildesheim: 3
- Wolfenbüttel: 3
- Celle: 6
- Goslar: 14
- Gifhorn: 4
- Lehrte: 1

Alle 13 Bestandsstädte berücksichtigt. Salzgitter, Helmstedt und Königslutter lieferten für diesen Durchgang keine weiteren passenden Einträge. Nur belegte Kategorien verwendet; keine künstliche Auffüllung je Stadt.

## Auswahl und Prüfung

Vollständige Adresse aus Ortsdatensatz oder eindeutig enthaltendem Gebäude; Ergänzungen mit Feldherkunft. Gebäude mit mehreren erfassten Locations ausgeschlossen. Bei POIs eindeutige Gebäudezuordnung, maximal 10 m Verschiebung, sonst Ausschluss. Gebäudekontur ist keine bestätigte Geschäftsfläche; weitere Nutzungen oder Etagen können vorhanden sein.

Kreisradius aus Randabstand minus 3,3 m, mindestens 2 m. 100 Konturen auf Ringabschluss, Fläche und Segmentkreuzung geprüft. 360 Randpunkte je Kreis innerhalb der Kontur. Keine Überschneidungen mit bisherigen/neuen Location-Radien, Stadt-/Deutschland-Chats ausgenommen. Tatsächlich gespeicherte Koordinaten separat geprüft: maximal 0,213 m Rundung, Innenpuffer mindestens 3 m. Kleinster gespeicherter Abstand zwischen Kreisrändern: 9,090 m.

Lokaler Import: 328 -> 428, alle 328 bisherigen Einträge unverändert. Zweiter Durchlauf erkennt 100 bereits vorhandene Einträge. Transaktion und Dublettenprüfung; keine Neuinstallation oder Überschreibung.

## Nutzung

`php tools/import_location_expansion.php --batch2`: Prüfung ohne Schreiben.
`php tools/import_location_expansion.php --batch2 --apply-local`: lokale additive Übernahme.
`python3 tools/check_location_expansion.py BESTAND.json --batch2`: Geometrie-/Abstandsprüfung.
`tools/prepare_mixed_expansion.py`: Auswahl aus Venue-JSON, Bestands-JSON und Gebäude-JSON.

locations.geojson enthält alle 100 Polygonkonturen in WGS84 (Longitude/Latitude); locations.csv die Prüfliste. Polygon-Zutrittsprüfung bleibt noch nicht aktiv, die App nutzt Radien. Handy-GPS kann driften und kleine Radien können den Zugang erschweren. Vor produktiver Freigabe Standort-/GPS-/Betreiberprüfung. Kein Live-Deployment.
