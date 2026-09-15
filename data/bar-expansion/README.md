# Weitere 100 Bars/Kneipen – lokaler Prüfstand

Daten: © OpenStreetMap contributors, ODbL 1.0, https://www.openstreetmap.org/copyright
Quellen-ID, Version und Zeitstempel sind pro Ort und zugehörigem Gebäude enthalten. Keine Betreiberbestätigung oder aktuelle Öffnungsgarantie. Ursprünglicher gemischter 100er-Katalog bleibt unverändert.

## Verteilung

- Braunschweig: 15
- Wolfsburg: 2
- Hannover: 50
- Hildesheim: 8
- Wolfenbüttel: 7
- Celle: 3
- Goslar: 5
- Helmstedt: 2
- Gifhorn: 3
- Königslutter am Elm: 3
- Lehrte: 2

Peine und Salzgitter wurden geprüft, lieferten unter den gewählten Einschränkungen keine zusätzlichen Kandidaten. Keine Restaurants als Bars umklassifiziert: nur OSM amenity=bar/pub.

## Geometrie und Grenzen

Bei einem POI muss genau ein enthaltendes Gebäude gefunden sein. Adressbestandteile dürfen aus diesem Gebäude ergänzt werden, mit Feldliste als Herkunft. Widersprüche ausgeschlossen. Bei mehreren Bar-POIs im Gebäude wird es ausgeschlossen. Die Gebäudegrundfläche ist weiterhin keine bestätigte Geschäfts-/Mietfläche; andere Nutzungen oder Etagen sind damit nicht ausgeschlossen.

OSM-POI bleibt erhalten, wenn der Randabstand reicht. Sonst maximal 10 m Verschiebung zu einem innenliegenden Punkt. Radius aus minimalem Randabstand minus 3,3 m, mindestens 2 m. Kreis statt Rechteck; schmale und lange Gebäude werden nicht vollständig erfasst. Umlaut-URLs werden per IDNA/Prozentkodierung normalisiert; Ursprungswert bleibt erhalten.

Geprüft: 100 eindeutige Einträge und Gebäudekonturen, vollständige Adressen, geschlossene Ringe, Fläche, Segmentkreuzungen, 360 Kreisrandpunkte je Kontur. Keine Überschneidungen mit neuen oder bestehenden Location-Radien; vor Rundung mindestens 5,5 m Abstand. Stadt-/Deutschland-Chats bewusst ausgenommen.

Handy-GPS kann driften. Kleine Radien können vor Ort den Zugang erschweren; keine Garantie, dass nur Menschen innerhalb der Geschäftsfläche Zugang erhalten. Polygon-Zutrittsprüfung ist noch nicht aktiv. Kartenmarker-Design wurde nicht verändert.

## Import und Übergabe

`php tools/import_location_expansion.php --bars` prüft ohne Änderungen.
`php tools/import_location_expansion.php --bars --apply-local` ergänzt die lokale DB transaktional und mit Dublettenprüfung. OSM-Schlüssel verhindert erneute Anlage. Keine Neuinstallation, keine Überschreibung.

`python3 tools/check_location_expansion.py BESTAND.json --bars` prüft Geometrie und Abstände.
`prepare_bar_expansion.py` benötigt Venue-Overpass-JSON, Bestands-JSON und Gebäude-Overpass-JSON. Gebäudeabfrage: way(around.bars:25)["building"]; out meta geom; mit zuvor ausgewählten Bar-Nodes. Nur tatsächlich enthaltende Konturen werden zugeordnet, keine nächstgelegenen Häuser geraten.

locations.json = Quellen-/Parameterregister; locations.geojson = 100 Polygone in WGS84, Longitude/Latitude; locations.csv = Prüfliste.

## Ergebnis

100 lokal importiert, Bestand 228 -> 328, alle 228 vorherigen Einträge unverändert. Wiederholungsprüfung erkennt alle 100. Radien stimmen nach Speicherung; maximale Koordinatenrundung 0,210 m, verbleibender Innenabstand mindestens 3 m. Erster Importversuch wurde wegen einer Umlaut-URL vollständig zurückgerollt; erfolgreiche Wiederholung nach URL-Normalisierung.

Kein Live-Deployment. Prüfung durch Mira/Gizmore vor Produktivübernahme erforderlich.
