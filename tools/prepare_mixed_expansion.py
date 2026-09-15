"""Build a source-labelled local catalogue; never fabricate boundaries or addresses."""
import json, math, re, sys, unicodedata
from pathlib import Path
from datetime import datetime, timezone
from urllib.parse import urlsplit,urlunsplit,quote
root=Path(__file__).resolve().parents[1]
raw=json.loads(Path(sys.argv[1]).read_text())
buildings=json.loads(Path(sys.argv[3]).read_text())["elements"]
existing=json.loads(Path(sys.argv[2]).read_text())
cities=['Braunschweig','Wolfsburg','Peine','Hannover','Hildesheim','Salzgitter','Wolfenbüttel','Celle','Goslar','Helmstedt','Gifhorn','Königslutter am Elm','Lehrte']
def norm(s): return re.sub(r'[^a-z0-9]','',unicodedata.normalize('NFKD',s.casefold()).encode('ascii','ignore').decode())
def distance(a,b):
 return math.hypot((a[0]-b[0])*111320*math.cos(math.radians(a[1])),(a[1]-b[1])*111320)
def contains(pt,ring):
 x,y=pt; inside=False
 for a,b in zip(ring,ring[1:]):
  if (a[1]>y)!=(b[1]>y) and x<(b[0]-a[0])*(y-a[1])/(b[1]-a[1])+a[0]: inside=not inside
 return inside
def clearance(pt,ring):
 scale=111320*math.cos(math.radians(pt[1]));best=1e20
 for a,b in zip(ring,ring[1:]):
  ax,ay=(a[0]-pt[0])*scale,(a[1]-pt[1])*111320
  bx,by=(b[0]-pt[0])*scale,(b[1]-pt[1])*111320
  dx,dy=bx-ax,by-ay;t=max(0,min(1,-(ax*dx+ay*dy)/(dx*dx+dy*dy))) if dx*dx+dy*dy else 0
  best=min(best,math.hypot(ax+t*dx,ay+t*dy))
 return best if contains(pt,ring) else -best
def interior(ring):
 xs=[p[0] for p in ring];ys=[p[1] for p in ring]
 xmin,xmax,ymin,ymax=min(xs),max(xs),min(ys),max(ys)
 best=None;score=-1e20
 for level in range(3):
  dx=(xmax-xmin)/20;dy=(ymax-ymin)/20
  for i in range(21):
   for j in range(21):
    pt=[xmin+i*dx,ymin+j*dy];c=clearance(pt,ring)
    if c>score:best,score=pt,c
  xmin,xmax=best[0]-dx,best[0]+dx;ymin,ymax=best[1]-dy,best[1]+dy
 return best,score
def normalize_url(url):
 if not url:return None
 try:
  u=urlsplit(url if '://' in url else 'https://'+url)
  if u.scheme not in ('http','https') or not u.hostname or u.username or u.password:return None
  host=u.hostname.encode('idna').decode('ascii')
  if u.port:host+=':'+str(u.port)
  return urlunsplit((u.scheme,host,quote(u.path,safe='/%:@'),quote(u.query,safe='=&%/:?+@'),quote(u.fragment,safe='%')))
 except (ValueError,UnicodeError):return None
# Associate POIs only to containing buildings, never to the closest unrelated house.
venues=[e for e in raw['elements'] if e.get('tags',{}).get('amenity') in ('bar','pub','cafe','restaurant','cinema','theatre','library')]
candidates={c:[] for c in cities};rejected={}
def reject(reason):rejected[reason]=rejected.get(reason,0)+1
def ring_for(e):
 r=[[p['lon'],p['lat']] for p in e.get('geometry',[])]
 return r if len(r)>=4 and r[0]==r[-1] and e.get('tags',{}).get('building') not in (None,'no') else None
outlines=[(e,ring_for(e)) for e in buildings];outlines=[(e,r) for e,r in outlines if r]
# Spatial buckets avoid scanning every building for every venue in larger extracts.
building_grid={}; venue_grid={}
def cell(point):return (math.floor(point[0]*200),math.floor(point[1]*200))
def cells_for(ring):
 xs=[p[0] for p in ring];ys=[p[1] for p in ring]
 lo=cell([min(xs),min(ys)]);hi=cell([max(xs),max(ys)])
 return ((x,y) for x in range(lo[0],hi[0]+1) for y in range(lo[1],hi[1]+1))
for b,r in outlines:
 for key in cells_for(r):building_grid.setdefault(key,[]).append((b,r))
for venue in venues:
 if venue['type']=='node':venue_grid.setdefault(cell([venue['lon'],venue['lat']]),[]).append(venue)
def venues_in(ring):
 for key in cells_for(ring):
  yield from venue_grid.get(key,[])
for e in venues:
 t=dict(e.get('tags',{}));name=t.get('name','')
 if not name or len(name)>64 or any(k in t for k in ('disused','abandoned','demolished','end_date')):reject('inactive_or_unnamed');continue
 if e['type']=='way' and ring_for(e):b=e;ring=ring_for(e);original=None
 elif e['type']=='node':
  original=[e['lon'],e['lat']]
  matches=[(b,r) for b,r in building_grid.get(cell(original),[]) if contains(original,r)]
  if len(matches)!=1:reject('no_unique_containing_building');continue
  b,ring=matches[0]
 else:reject('unsupported_geometry');continue
 if any((x['type'],x['id'])!=(e['type'],e['id']) and x['type']=='node' and contains([x['lon'],x['lat']],ring) for x in venues_in(ring)):reject('multiple_venues_in_building');continue
 if any(t.get(k) and b.get('tags',{}).get(k) and norm(t[k])!=norm(b['tags'][k]) for k in ('addr:city','addr:street','addr:housenumber','addr:postcode')):reject('address_conflict');continue
 inherited=[]
 for k in ('addr:city','addr:street','addr:housenumber','addr:postcode'):
  if not t.get(k) and b.get('tags',{}).get(k):t[k]=b['tags'][k];inherited.append(k)
 city=t.get('addr:city')
 if city not in cities or not all(t.get(k) for k in ('addr:street','addr:housenumber')) or not re.fullmatch(r'\d{5}',t.get('addr:postcode','')):reject('address_incomplete_or_outside_cities');continue
 if original and clearance(original,ring)>=5.3:point=original;edge=clearance(point,ring);method='OSM_POI_INSIDE_BUILDING'
 else:
  point,edge=interior(ring);method='INTERIOR_MAX_CLEARANCE_GRID'
  if original and distance(original,point)>10:reject('pin_shift_over_10m');continue
 radius=math.floor((edge-3.3)*10)/10
 if radius<2:reject('building_too_narrow');continue
 street=t['addr:street']+' '+t['addr:housenumber']
 if any((norm(name)==norm(x['room_name']) and (x.get('address_city')==city or distance(point,[float(x['room_pos_lng']),float(x['room_pos_lat'])])<500)) or (norm(street)==norm(x.get('address_street') or '') and city==x.get('address_city')) for x in existing):reject('existing_name_or_address');continue
 if any(str(x.get('room_category')) not in ('1','2','10') and distance(point,[float(x['room_pos_lng']),float(x['room_pos_lat'])])<radius+float(x['room_radius'])*1000+5.5 for x in existing):reject('existing_radius_conflict');continue
 entry={'key':f"osm-{e['type']}-{e['id']}",'name':name,'city':city,'street':street,'zip':t['addr:postcode'],'category':{'bar':3,'pub':4,'cafe':5,'restaurant':14,'cinema':12,'theatre':12,'library':16}[t['amenity']],'lat':point[1],'lng':point[0],'pin_method':method,'original_poi':original,'pin_shift_m':distance(original,point) if original else 0,'source_url':f"https://www.openstreetmap.org/{e['type']}/{e['id']}",'source_version':e.get('version'),'source_timestamp':e.get('timestamp'),'building_source_url':f"https://www.openstreetmap.org/way/{b['id']}",'building_version':b.get('version'),'building_timestamp':b.get('timestamp'),'address_fields_from_building':inherited,'website_original':t.get('website',t.get('contact:website')),'website':normalize_url(t.get('website',t.get('contact:website'))),'polygon':{'type':'Polygon','coordinates':[ring]},'polygon_status':'CONTAINING_BUILDING_NOT_TENANCY_VERIFIED','status':'OSM_SOURCED_NOT_OPERATOR_VERIFIED','chat_radius_km':radius/1000,'boundary_clearance_m':edge,'boundary_margin_m':3.3,'radius_status':'CIRCLE_INSIDE_OSM_BUILDING_NOT_GPS_GUARANTEE','view_km':32.0}
 candidates[city].append(entry)
selected=[];counts={c:0 for c in cities}
pools={c:sorted(candidates[c],key=lambda x:(x['pin_method']!='OSM_POI_INSIDE_BUILDING',x['website'] is None,x['name'])) for c in cities}
while len(selected)<100:
 progress=False
 for city in sorted(cities,key=lambda c:counts[c]):
  while pools[city]:
   p=pools[city].pop(0)
   if any(p['building_source_url']==x['building_source_url'] or (city==x['city'] and (norm(p['name'])==norm(x['name']) or norm(p['street'])==norm(x['street']))) or distance([p['lng'],p['lat']],[x['lng'],x['lat']])<(p['chat_radius_km']+x['chat_radius_km'])*1000+5.5 for x in selected):reject('new_duplicate_or_radius_conflict');continue
   selected.append(p);counts[city]+=1;progress=True;break
  if len(selected)==100:break
 if not progress:break
out=root/'data'/('location-expansion-3' if '--batch3' in sys.argv else 'location-expansion-2');out.mkdir(exist_ok=True)
if len(selected)!=100: raise SystemExit(json.dumps({'selected':len(selected),'rejected':rejected}));
doc={'schema_version':1,'target_count':100,'actual_count':len(selected),'prepared_at':datetime.now(timezone.utc).isoformat(),'attribution':'© OpenStreetMap contributors','license':'ODbL-1.0','license_url':'https://www.openstreetmap.org/copyright','osm_base':raw.get('osm3s',{}).get('timestamp_osm_base'),'entries':selected,'rejections':rejected}
(out/'locations.json').write_text(json.dumps(doc,ensure_ascii=False,indent=2)+'\n')
features=[{'type':'Feature','geometry':e['polygon'],'properties':{k:v for k,v in e.items() if k!='polygon'}} for e in selected]
(out/'locations.geojson').write_text(json.dumps({'type':'FeatureCollection','attribution':doc['attribution'],'license':doc['license'],'features':features},ensure_ascii=False,indent=2)+'\n')
print(json.dumps({'selected':len(selected),'cities':counts,'rejected':rejected},ensure_ascii=False))
