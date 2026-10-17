"""Read-only public OSM discovery. Never imports or approves a chat boundary."""
import json, urllib.request, urllib.parse, time
from pathlib import Path
from datetime import datetime, timezone
ROOT = Path(__file__).resolve().parents[1] / 'data/location-expansion-4'
CITIES = ['Berlin','Hamburg','Frankfurt am Main','Kiel','München','Nürnberg','Augsburg','Regensburg']
GROUPS = {'cafe':'cafe','bar':'bar','pub':'bar','nightclub':'club','university':'university','college':'university','cinema':'leisure','museum':'leisure','zoo':'leisure','theme_park':'leisure','park':'leisure','water_park':'leisure','sports_centre':'leisure'}
QUOTAS = {'cafe':25,'bar':25,'club':15,'university':10,'leisure':25}
ROOT.mkdir(exist_ok=True)
BOUNDARIES=json.loads((ROOT/'city-boundaries.json').read_text())
for city in CITIES:
    target=ROOT/(city.lower().replace(' ','-')+'-candidates.json')
    if target.exists():
        print(city,'already collected',flush=True);continue
    boundary=BOUNDARIES[city]
    if boundary['osm_type']!='relation':raise ValueError('Expected administrative relation')
    area=3600000000+boundary['osm_id']
    query='[out:json][timeout:40];area('+str(area)+')->.city;(nwr(area.city)["amenity"~"^(cafe|bar|pub|nightclub|university|college|cinema)$"]["name"];nwr(area.city)["tourism"~"^(museum|zoo|theme_park)$"]["name"];nwr(area.city)["leisure"~"^(park|water_park|sports_centre)$"]["name"];);out center tags;'
    url='https://overpass.private.coffee/api/interpreter?'+urllib.parse.urlencode({'data':query})
    try:
        with urllib.request.urlopen(url,timeout=55) as response: raw=json.load(response)
        if raw.get('remark'): raise RuntimeError(raw['remark'])
        pools={k:[] for k in QUOTAS};seen=set()
        for e in raw.get('elements',[]):
            t=e.get('tags',{});kind=GROUPS.get(t.get('amenity') or t.get('tourism') or t.get('leisure'))
            if not kind or any(k in t for k in ('disused','abandoned','demolished','end_date')) or t.get('access')=='private':continue
            pos=e if e['type']=='node' else e.get('center',{})
            if 'lat' not in pos or 'lon' not in pos:continue
            key=(t['name'].casefold(),round(pos['lat'],3),round(pos['lon'],3))
            if key in seen:continue
            seen.add(key)
            pools[kind].append({'source_key':f"osm-{e['type']}-{e['id']}",'name':t['name'],'city':city,'category_group':kind,'source_url':f"https://www.openstreetmap.org/{e['type']}/{e['id']}",'website':t.get('website',t.get('contact:website')),'address':{k.removeprefix('addr:'):v for k,v in t.items() if k.startswith('addr:')},'source_point':{'lat':pos['lat'],'lng':pos['lon'],'method':'osm_node' if e['type']=='node' else 'bbox_center_not_verified_pin'},'status':'candidate_not_importable','operation_verified':False,'pin_verified':False,'chat_boundary':None,'chat_radius_km':None})
        entries=[]
        for kind,quota in QUOTAS.items():
            pool=sorted(pools[kind],key=lambda e:(not bool(e['website']),not bool(e['address'].get('street')),e['name']))
            entries.extend(pool[:quota])
        doc={'city':city,'city_boundary_source':'https://www.openstreetmap.org/relation/'+str(boundary['osm_id']),'fetched_at':datetime.now(timezone.utc).isoformat(),'osm_base':raw.get('osm3s',{}).get('timestamp_osm_base'),'attribution':'© OpenStreetMap contributors','license':'ODbL-1.0','license_url':'https://www.openstreetmap.org/copyright','status':'discovery_only_not_importable','available':{k:len(v) for k,v in pools.items()},'entries':entries}
        target.write_text(json.dumps(doc,ensure_ascii=False,indent=2)+'\n')
        print(city,len(entries),'selected',doc['available'],'OSM',doc['osm_base'],flush=True)
    except Exception as exc:print(city,'FAILED',type(exc).__name__,str(exc),flush=True)
    time.sleep(1)
