"""Build the high-resolution final globe frame for the scroll zoom from Natural Earth public-domain land.
Run: python3 tools/render_globe.py (Pillow, numpy). No network at build/runtime.
"""
from pathlib import Path
import json
import numpy as np
from PIL import Image, ImageDraw
ROOT=Path(__file__).resolve().parents[1]
W,H=8192,4096
mask=Image.new('L',(W,H));draw=ImageDraw.Draw(mask)
for feature in json.loads((ROOT/'www/data/ne_110m_land.geojson').read_text())['features']:
    geom=feature['geometry'];polys=[geom['coordinates']] if geom['type']=='Polygon' else geom['coordinates']
    for poly in polys:
        for i,ring in enumerate(poly):
            draw.polygon([((lon+180)/360*W,(90-lat)/180*H) for lon,lat in ring],fill=255 if i==0 else 0)
land=np.array(mask)/255
size=2048;frames=64

y,x=np.mgrid[0:size,0:size].astype(np.float32)
x=(x+.5-size/2)/(size/2-3);y=-(y+.5-size/2)/(size/2-3)
r=x*x+y*y;valid=r<1;z=np.sqrt(np.maximum(0,1-r))
# A fixed camera looking slightly down on Europe; rotate the planet beneath it.
tilt=np.deg2rad(18)
yw=y*np.cos(tilt)+z*np.sin(tilt);zw=z*np.cos(tilt)-y*np.sin(tilt)
lat=np.arcsin(np.clip(yw,-1,1));base=np.arctan2(x,zw)
light=np.clip(x*-.38+y*.46+z*.80,0,1)
rim=np.power(1-z,3)
for frame in [frames-1]:
    lon=base+np.deg2rad(-16+frame/(frames-1)*132)
    u=((lon/(2*np.pi)+.5)%1*(W-1)).astype(int)
    v=np.clip((.5-lat/np.pi)*(H-1),0,H-1).astype(int)
    ground=land[v,u][...,None]
    ocean=np.array([26,47,78]);earth=np.array([153,157,187])
    rgb=(ocean*(1-ground)+earth*ground)*(.34+.76*light[...,None])
    # Soft atmospheric edge and a restrained sea highlight, no random sparkle.
    rgb+=rim[...,None]*np.array([19,35,62])
    spec=np.power(np.clip(x*-.25+y*.3+z*.92,0,1),26)[...,None]
    rgb+=spec*(1-ground)*np.array([12,18,25])
    rgba=np.zeros((size,size,4),dtype=np.uint8);rgba[:,:,:3]=np.clip(rgb,0,255)
    rgba[:,:,3]=(np.clip((1-np.sqrt(r))*(size/2-3),0,1)*255).astype('uint8')
    Image.fromarray(rgba).save(ROOT/'www/img/linkuup-globe-zoom.webp',quality=94,method=6)
print('Zoom frame generated: 2048 x 2048')
