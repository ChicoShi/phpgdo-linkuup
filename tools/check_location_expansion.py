"""Structural/geometry checks; no claim of operator or boundary approval."""
import json,math,sys
from pathlib import Path
from collections import Counter
batch3='--batch3' in sys.argv
if batch3:sys.argv.remove('--batch3')
batch2='--batch2' in sys.argv
if batch2:sys.argv.remove('--batch2')
bars='--bars' in sys.argv
if bars:sys.argv.remove('--bars')
root=Path(__file__).resolve().parents[1]/'data'/('bar-expansion' if bars else ('location-expansion-3' if batch3 else ('location-expansion-2' if batch2 else 'location-expansion')))
d=json.loads((root/'locations.json').read_text()); e=d['entries']
assert len(e)==100 and len({x['key'] for x in e})==100
assert len(Counter(x['city'] for x in e))>0
if not bars and not batch2 and not batch3:assert len(Counter(x['city'] for x in e))==13
if bars:assert all(x['category'] in (3,4) and x['building_source_url'] for x in e)
assert len({(x['city'],x['street'].casefold()) for x in e})==100
polygons=0
for x in e:
 assert len(x['name'])<=64 and all(x[k] for k in ['name','street','zip','city','source_url'])
 assert math.isfinite(x['lat']) and math.isfinite(x['lng'])
 p=x['polygon']
 assert p is not None
 assert x['chat_radius_km']*1000+2.999<=x['boundary_clearance_m']
 polygons+=1;r=p['coordinates'][0]
 assert r[0]==r[-1] and len(set(map(tuple,r[:-1])))>=3
 area=sum(a[0]*b[1]-b[0]*a[1] for a,b in zip(r,r[1:]))/2
 assert abs(area)>1e-12, x['key']
 def orient(a,b,c):return (b[0]-a[0])*(c[1]-a[1])-(b[1]-a[1])*(c[0]-a[0])
 for i,(a,b) in enumerate(zip(r,r[1:])):
  for j in range(i+2,len(r)-1):
   if i==0 and j==len(r)-2:continue
   c,f=r[j:j+2]
   assert not (orient(a,b,c)*orient(a,b,f)<0 and orient(c,f,a)*orient(c,f,b)<0),x['key']
g=json.loads((root/'locations.geojson').read_text())
assert len(g['features'])==100
for f,x in zip(g['features'],e):assert f['properties']['key']==x['key']
print(json.dumps({'locations':100,'cities':dict(Counter(x['city'] for x in e)),'osm_contours':polygons,'missing_contours':100-polygons},ensure_ascii=False))

def contains(pt,ring):
 x,y=pt;inside=False
 for a,b in zip(ring,ring[1:]):
  if (a[1]>y)!=(b[1]>y) and x<(b[0]-a[0])*(y-a[1])/(b[1]-a[1])+a[0]:inside=not inside
 return inside
def dist(a,b):return math.hypot((a['lng']-b['lng'])*111320*math.cos(math.radians(a['lat'])),(a['lat']-b['lat'])*111320)
for i,a in enumerate(e):
 r=a['chat_radius_km']*1000
 for deg in range(360):
  angle=math.radians(deg)
  pt=[a['lng']+math.cos(angle)*r/(111320*math.cos(math.radians(a['lat']))),a['lat']+math.sin(angle)*r/111320]
  assert contains(pt,a['polygon']['coordinates'][0]),a['key']
 for b in e[i+1:]:assert dist(a,b)>=(a['chat_radius_km']+b['chat_radius_km'])*1000+4.99
if len(sys.argv)>1:
 old=json.loads(Path(sys.argv[1]).read_text())
 for a in e:
  for b in old:
   if str(b.get('room_category')) in ('1','2','10'):continue
   q={'lat':float(b['room_pos_lat']),'lng':float(b['room_pos_lng'])}
   assert dist(a,q)>=a['chat_radius_km']*1000+float(b['room_radius'])*1000+4.99,(a['key'],b['room_id'])
print('360 circle samples per building inside; all new radii separated with 5 m gap; existing venue radii checked when supplied.')
