# 100 zusätzliche Locations – lokaler Prüfstand

Quelle: © OpenStreetMap contributors, ODbL 1.0.
https://www.openstreetmap.org/copyright
Geometrieabfrage: https://dev.overpass-api.de/overpass-doc/en/targets/formats.html

## Umfang

100 neue Locations, alle mit vollständiger OSM-Adresse, benannter Gebäudekontur und Quellen-ID/Version. Keine Betreiberfreigabe oder Vor-Ort-Vermessung behauptet. Gebäude können mehrere Nutzungen enthalten; Kontur ist keine automatisch bestätigte Geschäftsfläche.

- Braunschweig: 10
- Wolfsburg: 10
- Peine: 10
- Hannover: 10
- Hildesheim: 4
- Salzgitter: 10
- Wolfenbüttel: 9
- Celle: 9
- Goslar: 9
- Helmstedt: 4
- Gifhorn: 9
- Königslutter am Elm: 4
- Lehrte: 2

## Position und Radius

Punkt über dreistufige Rasterverfeinerung an einer innenliegenden Stelle mit großem Randabstand gewählt; kein behaupteter Eingangspunkt oder exaktes mathematisches Optimum. Radius = abgerundeter minimaler Randabstand minus 3 m. Auswahl nur ab 2 m Radius. Aktueller Bereich 2,0–21,3 m.

Alle 100 Konturen auf geschlossene Ringe, Fläche und echte Segmentkreuzungen geprüft. Pro Kreis 360 Randpunkte innerhalb der Kontur geprüft. Neue Kreise mindestens 5 m voneinander und von bisherigen Location-Kreisen getrennt; übergeordnete Stadt-/Deutschland-Chats sind ausdrücklich ausgenommen. Ein schmaler oder langer Bau wird nicht vollständig vom Kreis erfasst. Innenhöfe/Multipolygone wurden nicht als einfacher Ring übernommen.

Dies ist eine geometrische Auswahl, keine GPS-Garantie: Handy-GPS kann außerhalb des Gebäudes liegen oder driften, kleine Radien können innen den Chat sperren. Vor Live-Freigabe reale Standorte, Geschäftsflächen, GPS-Verhalten und Betreiberstatus prüfen.

## Dateien und Übernahme

- locations.json: vollständiges Daten- und Quellenregister.
- locations.geojson: 100 echte Polygonkonturen; Koordinatenfolge Longitude, Latitude (WGS84).
- locations.csv: lesbare Prüfliste.
- ../../tools/import_location_expansion.php: standardmäßig nur Prüfung; --apply-local importiert ausschließlich in lokale DB. Transaktion, Dublettenprüfung, OSM-Key verhindert Wiederholung; keine bestehenden Einträge überschrieben.
- ../../tools/check_location_expansion.py: Struktur-/Geometrieprüfung; optional Pfad zu Bestands-JSON für Abstandsprüfung.
- ../../tools/prepare_location_expansion.py: reproduzierbare Auswahl aus Overpass-JSON und bestehendem Katalog.

Die App verwendet weiter den Radius. Die GeoJSON-Konturen sind für eine spätere Polygon-Anbindung vorbereitet, noch keine aktive serverseitige Polygon-Zutrittskontrolle. Das Aussehen eines Kartenmarkers wurde nicht geändert.

## Lokale Prüfung

100 Einträge importiert; Bestand 128 -> 228. Alle 128 bisherigen Einträge unverändert. Zweiter Prüflauf erkennt sämtliche 100 als vorhanden. Gespeicherte Radien stimmen; maximale Koordinatenrundung durch bestehende FLOAT-Spalten: 0,210 m. Der etwa 3-m-Innenpuffer bleibt damit erhalten. Keine Änderung der DB-Spaltentypen.

Kein Live-Deployment. Review durch Mira/Gizmore vor produktiver Übernahme.
