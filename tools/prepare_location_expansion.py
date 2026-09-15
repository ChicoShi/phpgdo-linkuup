"""Build a source-labelled local catalogue; never fabricate boundaries or addresses."""
import json, math, re, sys, unicodedata
from pathlib import Path
from datetime import datetime, timezone
from urllib.parse import urlsplit,urlunsplit,quote
root=Path(__file__).resolve().parents[1]
raw=json.loads(Path(sys.argv[1]).read_text())
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
cat={'cafe':5,'restaurant':14,'bar':3,'pub':4,'nightclub':11,'library':16,'cinema':12,'theatre':12,'museum':12}
candidates={c:[] for c in cities}
for e in raw['elements']:
 t=e.get('tags',{}); city=t.get('addr:city'); name=t.get('name','')
 if city not in cities or not name or not t.get('addr:street') or not t.get('addr:housenumber') or not re.fullmatch(r'\d{5}',t.get('addr:postcode','')): continue
 if any(k in t for k in ('disused','abandoned','demolished','end_date')): continue
 category=cat.get(t.get('amenity',t.get('tourism')))
 if not category: continue
 ring=[[p['lon'],p['lat']] for p in e.get('geometry',[])]
 polygon=ring if len(ring)>=4 and ring[0]==ring[-1] and e['type']=='way' else None
 if not polygon or t.get('building') in (None,'no') or t.get('type')=='multipolygon':continue
 point,edge_clearance=interior(ring)
 radius=math.floor((edge_clearance-3.3)*10)/10
 if radius<2:continue
 method='INTERIOR_MAX_CLEARANCE_GRID'
 if any(str(x.get('room_category')) not in ('1','2','10') and distance(point,[float(x['room_pos_lng']),float(x['room_pos_lat'])]) < radius+float(x['room_radius'])*1000+5.5 for x in existing):continue
 street=t['addr:street']+' '+t['addr:housenumber']
 if any(norm(name)==norm(x['room_name']) and (x.get('address_city')==city or distance(point,[float(x['room_pos_lng']),float(x['room_pos_lat'])])<500) or (norm(street)==norm(x.get('address_street') or '') and city==x.get('address_city')) for x in existing): continue
 entry={'key':f"osm-{e['type']}-{e['id']}",'name':name,'city':city,'street':street,'zip':t['addr:postcode'],'category':category,'lat':point[1],'lng':point[0],'pin_method':method,'source_url':f"https://www.openstreetmap.org/{e['type']}/{e['id']}",'source_version':e.get('version'),'source_timestamp':e.get('timestamp'),'website_original':t.get('website',t.get('contact:website')),'website':normalize_url(t.get('website',t.get('contact:website'))),'polygon':{'type':'Polygon','coordinates':[polygon]} if polygon else None,'polygon_status':'OSM_CONTOUR_REVIEW_REQUIRED' if polygon else 'MISSING','status':'OSM_SOURCED_NOT_OPERATOR_VERIFIED','chat_radius_km':radius/1000,'boundary_clearance_m':edge_clearance,'boundary_margin_m':3.3,'radius_status':'CIRCLE_INSIDE_OSM_BUILDING_NOT_GPS_GUARANTEE','view_km':32.0}
 candidates[city].append(entry)
selected=[]
pools={c:sorted(candidates[c],key=lambda x:(x['website'] is None,x['name'])) for c in cities}
counts={c:0 for c in cities}
while len(selected)<100:
 progress=False
 for city in sorted(cities,key=lambda c:counts[c]):
  while pools[city]:
   p=pools[city].pop(0)
   if any((p['city']==x['city'] and (norm(p['name'])==norm(x['name']) or norm(p['street'])==norm(x['street']))) or distance([p['lng'],p['lat']],[x['lng'],x['lat']])<(p['chat_radius_km']+x['chat_radius_km'])*1000+5.5 for x in selected):continue
   selected.append(p);counts[city]+=1;progress=True;break
  if len(selected)==100:break
 if not progress:break
for city in cities:print(city,len(candidates[city]),'candidates;',counts[city],'selected')
if len(selected)!=100: raise SystemExit(f'Expected 100, found {len(selected)}; no incomplete catalogue written')
batch='location-expansion-2' if '--batch2' in sys.argv else 'location-expansion'
out=root/'data'/batch
out.mkdir(exist_ok=True)
doc={'schema_version':1,'fetched_at':datetime.now(timezone.utc).isoformat(),'attribution':'© OpenStreetMap contributors','license':'ODbL-1.0','license_url':'https://www.openstreetmap.org/copyright','osm_base':raw.get('osm3s',{}).get('timestamp_osm_base'),'entries':selected}
(out/'locations.json').write_text(json.dumps(doc,ensure_ascii=False,indent=2)+'\n')
features=[]
for p in selected:
 props={k:v for k,v in p.items() if k!='polygon'}
 features.append({'type':'Feature','geometry':p['polygon'] or {'type':'Point','coordinates':[p['lng'],p['lat']]},'properties':props})
(out/'locations.geojson').write_text(json.dumps({'type':'FeatureCollection','attribution':doc['attribution'],'license':doc['license'],'features':features},ensure_ascii=False,indent=2)+'\n')
print('Total',len(selected),'real contours',sum(x['polygon'] is not None for x in selected))
