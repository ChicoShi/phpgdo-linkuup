# Weltkugel: Datenquelle und Erzeugung

`ne_110m_land.geojson`: Natural Earth, Landflächen 1:110 Millionen, Public Domain.
Quelle: https://github.com/nvkelso/natural-earth-vector/blob/master/geojson/ne_110m_land.geojson
Lizenz: https://github.com/nvkelso/natural-earth-vector/blob/master/LICENSE.md
Abruf: 12.09.2026. Die eingecheckte Datei ist die reproduzierbare Datenbasis.

`python3 tools/render_globe.py` erzeugt mit Pillow und NumPy das WebP-Atlasbild.
64 orthografisch projizierte Ansichten à 320 × 320 Pixel; 8 × 8 Kacheln.
Laufzeit: ausschließlich lokale Bilddatei, keine Karten-API oder WebGL-Abhängigkeit.
Die Kugel dient der Illustration, nicht der Navigation oder Vermessung.
