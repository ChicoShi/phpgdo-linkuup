/* Decorative city traffic: generated locally, never represents live users. */
(() => {
 'use strict';
 const init=()=>{
  const main=document.querySelector('main.lup-arrival');
  if(!main||main.querySelector('.lup-living-field'))return;
  const ns='http://www.w3.org/2000/svg',make=(name,attrs={})=>{const el=document.createElementNS(ns,name);for(const [k,v] of Object.entries(attrs))el.setAttribute(k,v);return el;};
  const field=document.createElement('div');field.className='lup-living-field';field.setAttribute('aria-hidden','true');
  const svg=make('svg',{viewBox:'0 0 1200 900',preserveAspectRatio:'xMidYMid slice'}),city=make('g',{'class':'lup-living-city'});
  const roads=make('g',{'class':'lup-living-roads'}),blocks=make('g',{'class':'lup-living-blocks'});
  // Isometric projection gives the city depth without a GPU scene.
  const project=(x,y)=>[600+(x-y)*75,190+(x+y)*37];
  const d=(points)=>points.map((p,i)=>(i?'L':'M')+p.map(n=>n.toFixed(1)).join(' ')).join(' ');
  for(let i=-7;i<=7;i++){
   roads.append(make('path',{d:d([project(i,-7),project(i,7)])}),make('path',{d:d([project(-7,i),project(7,i)])}));
   for(let j=-7;j<=7;j++){
    if((i*13+j*7)%4!==0)continue;
    const corners=[project(i+.17,j+.17),project(i+.78,j+.17),project(i+.78,j+.78),project(i+.17,j+.78)],h=10+Math.abs(i*7+j*13)%28;
    const top=corners.map(([x,y])=>[x,y-h]);
    blocks.append(make('path',{d:d(top)+'Z','class':'lup-city-roof'}));
    blocks.append(make('path',{d:d([top[1],corners[1],corners[2],corners[3],top[3]])}));
    blocks.append(make('path',{d:d([top[2],corners[2]])}));
   }
  }
  city.append(roads,blocks);svg.append(city);field.append(svg);main.prepend(field);main.classList.add('lup-has-living-field');
  const trajectories=[[[ -7,-4],[2,-4],[2,6]],[[6,-7],[6,1],[-5,1]],[[-7,5],[-2,5],[-2,-7]],[[7,4],[4,4],[4,-6]],[[-5,-7],[-5,-2],[7,-2]]];
  const traffic=trajectories.map((route,i)=>{
   const path=make('path',{d:d(route.map(p=>project(...p))),'class':'lup-traffic-route'});city.append(path);
   const node=make('g',{'class':'lup-traffic-light'});node.append(make('circle',{r:11,'class':'lup-traffic-halo'}),make('circle',{r:2.6}));city.append(node);
   return {path,node,length:path.getTotalLength(),offset:i*.21};
  });
  const reduced=matchMedia('(prefers-reduced-motion: reduce)');
  let visible=true,raf=0,last=0,elapsed=0,depth=0,targetX=0,targetY=0,x=0,y=0;
  const render=(time)=>{
   raf=0;if(document.hidden||!visible||reduced.matches)return;
   if(time-last<33){raf=requestAnimationFrame(render);return;}
   elapsed+=Math.min(time-last,60)/1000;last=time;
   depth+=(scrollY*.035-depth)*.08;x+=(targetX-x)*.06;y+=(targetY-y)*.06;
   city.setAttribute('transform',`translate(${x} ${-depth+y})`);
   traffic.forEach(({path,node,length,offset})=>{const p=path.getPointAtLength(((elapsed/26+offset)%1)*length);node.setAttribute('transform',`translate(${p.x} ${p.y})`);});
   raf=requestAnimationFrame(render);
  };
  const resume=()=>{cancelAnimationFrame(raf);raf=0;if(!document.hidden&&visible&&!reduced.matches){last=performance.now();raf=requestAnimationFrame(render);}if(reduced.matches){city.removeAttribute('transform');traffic.forEach(({node})=>node.style.opacity='0');}else traffic.forEach(({node})=>node.style.opacity='');};
  main.addEventListener('pointermove',e=>{if(e.pointerType==='mouse'){targetX=(e.clientX/innerWidth-.5)*16;targetY=(e.clientY/innerHeight-.5)*10;}},{passive:true});
  main.addEventListener('pointerleave',()=>{targetX=targetY=0;});
  new IntersectionObserver(([entry])=>{visible=entry.isIntersecting;field.hidden=!visible;resume();}).observe(main);
  reduced.addEventListener('change',resume);document.addEventListener('visibilitychange',resume);resume();
 };
 document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
