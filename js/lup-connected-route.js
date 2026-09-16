/* One reversible scroll timeline: button → city → globe → categories → principles. */
(() => {
 'use strict';
 const init=()=>{
  const main=document.querySelector('main.lup-arrival'),pin=main?.querySelector('.lup-scroll-traveller'),source=main?.querySelector('.lup-world-pin'),chapter=main?.querySelector('#lup-arrival-journey');
  if(!pin||!source||!chapter)return;
  const sphere=chapter.querySelector('.lup-world-sphere'),stage=chapter.querySelector('.lup-journey-stage'),map=main.querySelector('.lup-scroll-map'),cta=main.querySelector('.lup-arrival-actions > a'),hero=main.querySelector('.lup-arrival-hero');
  const buildings=[...main.querySelectorAll('.lup-city-anchor')].sort((a,b)=>Number(a.dataset.route)-Number(b.dataset.route)),categories=[...main.querySelectorAll('.lup-destination')],categorySection=main.querySelector('#lup-arrival-categories'),invitation=chapter.querySelector('.lup-world-invitation');
  pin.innerHTML=source.outerHTML.replace('class="lup-world-pin"','class="lup-route-symbol"').replaceAll('lup-pin-gradient','lup-route-gradient');
  const symbol=pin.firstElementChild,shell=document.createElement('span');shell.className='lup-pin-button-shell';pin.append(shell);
  const ns='http://www.w3.org/2000/svg',morphSVG=document.createElementNS(ns,'svg');morphSVG.classList.add('lup-button-morph');
  morphSVG.innerHTML='<defs><linearGradient id="lup-morph-fill" x2="1" y2="1"><stop stop-color="#d3c4ef"/><stop offset="1" stop-color="#d3c4ef"/></linearGradient></defs><path fill="url(#lup-morph-fill)"/><circle fill="#171b35"/>';
  shell.append(morphSVG);const outline=morphSVG.querySelector('path'),eye=morphSVG.querySelector('circle'),label=document.createElement('span');label.className='lup-morph-label';label.innerHTML=cta.innerHTML;shell.append(label);
  const targetPath=source.querySelector('path'),targetLength=targetPath.getTotalLength();
  const targetPoints=Array.from({length:80},(_,i)=>{const q=targetPath.getPointAtLength(targetLength*i/80);return {x:(q.x-100)*32/270,y:(q.y-135)*32/270};});
  let buttonPoints=[];
  const feet=document.createElement('span');feet.className='lup-pin-feet';pin.append(feet);document.body.append(pin);pin.classList.add('lup-unified-pin');
  const trail=document.createElement('div');trail.className='lup-adventure-trail';trail.setAttribute('aria-hidden','true');document.body.append(trail);
  const marks=Array.from({length:14},()=>{const e=document.createElement('span');trail.append(e);return e;});
  const globeSteps=document.createElement('div');globeSteps.className='lup-globe-steps';globeSteps.setAttribute('aria-hidden','true');sphere.append(globeSteps);
  const prints=Array.from({length:48},()=>{const e=document.createElement('i');globeSteps.append(e);return e;});
  const icons=[...main.querySelectorAll('.lup-arrival-principle-flow > div > i')];
  const explanations=['Entdecke, welche Orte sich in deiner Nähe befinden.','Wähle den Ort und die Begegnung, die zu dir passen.','Begegne anderen aufmerksam und respektvoll.'];
  const notes=icons.map((el,i)=>{el.classList.add('lup-route-dock');const n=document.createElement('small');n.className='lup-principle-note';n.textContent=explanations[i];el.parentElement.append(n);return n;});
  const caption=document.createElement('p');caption.className='lup-tour-caption';caption.setAttribute('aria-hidden','true');categorySection.querySelector('.lup-destinations').after(caption);
  let manualCategory=false;categories.forEach(b=>b.addEventListener('click',()=>{manualCategory=true;caption.textContent=b.dataset.description;caption.style.setProperty('--tour-color',b.style.getPropertyValue('--place-color'));schedule();}));
  const reduced=matchMedia('(prefers-reduced-motion: reduce)'),clamp=x=>Math.max(0,Math.min(1,x)),ease=x=>{x=clamp(x);return x*x*(3-2*x);},mix=(a,b,t)=>a+(b-a)*t;
  // Shared tilted-sphere projection for the traveller and every planted footprint.
  const surface=(longitude,latitude=0)=>{const c=Math.cos(latitude),z=Math.cos(longitude)*c;return {x:Math.sin(longitude)*c*.46,y:(Math.sin(latitude)*.94-z*.342)*.46,front:z*.94+Math.sin(latitude)*.342};};
  let raf=0,shown=scrollY,last=0,g;
  const center=el=>{const r=el.getBoundingClientRect();return {x:r.left+r.width/2,y:r.top+scrollY+r.height/2};};
  const layout=()=>{
   const r=main.getBoundingClientRect(),c=chapter.getBoundingClientRect(),button=cta.getBoundingClientRect(),hr=hero.getBoundingClientRect(),cr=categorySection.getBoundingClientRect();
   const sample=document.createElementNS(ns,'path'),w=button.width/2,h=button.height/2,corner=Math.min(h,16);
   sample.setAttribute('d',`M0 ${-h} H${w-corner} Q${w} ${-h} ${w} ${-h+corner} V${h-corner} Q${w} ${h} ${w-corner} ${h} H${-w+corner} Q${-w} ${h} ${-w} ${h-corner} V${-h+corner} Q${-w} ${-h} ${-w+corner} ${-h} Z`);
   const perimeter=sample.getTotalLength();buttonPoints=targetPoints.map((_,i)=>sample.getPointAtLength(perimeter*i/80));
   label.style.font=getComputedStyle(cta).font;label.style.gap=getComputedStyle(cta).gap;
   const rail=r.left+Math.max(18,(r.width-1160)/2+14);
   g={rail,start:c.top+scrollY-(innerWidth<761?76:90),length:Math.max(1,chapter.offsetHeight-stage.offsetHeight),bottom:r.bottom+scrollY,top:r.top+scrollY,button:center(cta),buttonWidth:button.width,buttonHeight:button.height,heroStart:Math.max(0,button.top+scrollY-innerHeight*.77),heroEnd:hr.bottom+scrollY-innerHeight*.35,buildings:buildings.map(center),catStart:cr.top+scrollY-innerHeight*.5,catLength:Math.max(360,cr.height*.7),stops:icons.map(el=>({...center(el),el,t:center(el).y-innerHeight*.55}))};
   const x=rail-r.left,mapHeight=main.offsetHeight;let path=`M ${x} 0`;for(let y=0;y<mapHeight;y+=320)path+=` C ${x+10} ${y+100},${x-10} ${y+220},${x} ${y+320}`;
   map.setAttribute('viewBox',`0 0 ${r.width} ${mapHeight}`);map.querySelectorAll('path').forEach(p=>p.setAttribute('d',path));schedule();
  };
  const pose=(s,b)=>{
   const p=clamp((s-g.start)/g.length),hp=clamp((s-g.heroStart)/Math.max(1,g.heroEnd-g.heroStart));
   let x=g.rail+6*Math.sin(s/210),y=Math.min(innerHeight*.55,100+s*.45),scale=1,rotation=6*Math.cos(s/210),opacity=1,walk=0,morph=1;
   if(s<g.heroEnd&&g.buildings.length){
    // Reserve the first quarter for the handoff, then visit each doorway.
    const travel=clamp((hp-.32)/.46),q=travel*g.buildings.length,i=Math.min(g.buildings.length-1,Math.floor(q)),t=q-i;
    const from=i===0?{x:g.button.x,y:g.button.y-12}:g.buildings[i-1],to=g.buildings[i];
    morph=ease(hp/.32);
    if(hp<.32){x=g.button.x;y=g.button.y-s-12*morph;rotation=0;scale=1;}
    else if(hp<.78){
     const e=ease(clamp(t/.76));x=mix(from.x,to.x,e);y=mix(from.y,to.y,e)-s-Math.sin(e*Math.PI)*24;
     const inside=ease((t-.72)/.13)*(1-ease((t-.91)/.09));
     opacity=1-inside*.95;scale=1.15-inside*.5;rotation=Math.sin(e*Math.PI*2)*5;walk=1;
    }else{
     const e=clamp((hp-.78)/.22),launch=ease(e/.4),returning=ease((e-.4)/.6),lastHouse=g.buildings[g.buildings.length-1];
     const spotlightX=mix(lastHouse.x,Math.min(innerWidth-65,Math.max(65,lastHouse.x-45)),launch);
     const spotlightY=lastHouse.y-s-95*launch;
     x=mix(spotlightX,g.rail,returning);y=mix(spotlightY,innerHeight*.55,returning);
     scale=1.15+2.25*launch*(1-returning);rotation=-8*Math.sin(e*Math.PI*2);walk=0;
    }
    opacity*=s<g.heroStart?0:1;

   }else if(s>=g.start&&s<=g.start+g.length){
    const q=clamp((p-.14)/.7),a=Math.PI-q*Math.PI*2.5,join=ease(p/.14)*(1-ease((p-.84)/.16));
    const projected=surface(a),dx=projected.x*b.width,dy=projected.y*b.height;
    const ox=b.left+b.width/2+dx,oy=b.top+b.height/2+dy;
    x=mix(x,ox,join);y=mix(y,oy-14-Math.sin(q*Math.PI*48)**2*1.5,join);scale=mix(1,1.18+Math.cos(a)*.16,join);rotation=mix(rotation,Math.sin(q*Math.PI*48)*3,join);walk=join;
    opacity*=1-ease((.04-projected.front)/.18)*join;
   }else if(s>g.start+g.length){
    g.stops.forEach((stop,i)=>{
     const before=i?(g.stops[i-1].t+stop.t)/2:Math.max(g.start+g.length,stop.t-240),after=i<g.stops.length-1?(stop.t+g.stops[i+1].t)/2:stop.t+240;
     if(s<before||s>after)return;
     const t=clamp((s-before)/Math.max(1,after-before));
     // Approach, one complete wrap, return: keep the orbit intact at its edges.
     const enter=ease(t/.2),leave=ease((t-.8)/.2),join=enter*(1-leave);
     const orbit=ease((t-.2)/.6),angle=Math.PI+orbit*Math.PI*2;
     const radiusX=Math.min(39,Math.max(28,stop.x-18)),radiusY=37;
     x=mix(x,stop.x+Math.cos(angle)*radiusX,join);
     y=mix(y,stop.y-scrollY+Math.sin(angle)*radiusY,join);
     rotation=mix(rotation,Math.sin(angle)*12,join);scale=1+join*.12;
    });
   }
   opacity*=ease((g.bottom-scrollY-90)/160);return {x,y,scale,rotation,opacity,walk,morph,p,hp};
  };
  const draw=time=>{
   raf=0;if(document.hidden||!g)return;const still=reduced.matches||innerHeight<=480,dt=last?Math.min(48,time-last):16;last=time;
   shown=still?scrollY:shown+(scrollY-shown)*(1-Math.exp(-dt/160));if(Math.abs(shown-scrollY)<.03)shown=scrollY;
   const b=sphere.getBoundingClientRect(),v=pose(shown,b);
   document.dispatchEvent(new CustomEvent('lup:route-frame',{detail:{progress:still?.3:v.p,still}}));
   pin.style.transform=`translate3d(${v.x-12}px,${v.y-16}px,0) rotate(${v.rotation}deg) scale(${v.scale})`;pin.style.opacity=String(still?0:v.opacity);
   // One continuous outline, not a squeezed label crossfading with another icon.
   const m=v.morph,finish=ease((m-.96)/.04);
   outline.setAttribute('d',buttonPoints.map((pt,i)=>`${i?'L':'M'}${mix(pt.x,targetPoints[i].x,m).toFixed(2)} ${mix(pt.y,targetPoints[i].y,m).toFixed(2)}`).join(' ')+' Z');
   eye.setAttribute('cx','0');eye.setAttribute('cy',String((98-135)*32/270));eye.setAttribute('r',String(39*32/270*ease((m-.48)/.4)));
   eye.style.opacity=String(ease((m-.48)/.4));
   const stops=morphSVG.querySelectorAll('stop'),tint=ease((m-.35)/.65);
   const color=(a,b)=>'#'+a.map((v,i)=>Math.round(mix(v,b[i],tint)).toString(16).padStart(2,'0')).join('');
   stops[0].setAttribute('stop-color',color([211,196,239],[179,160,255]));stops[1].setAttribute('stop-color',color([211,196,239],[69,187,235]));
   shell.style.opacity=String(1-finish);symbol.style.opacity=String(finish);
   label.style.opacity=String(1-ease(m/.3));label.style.transform=`translate(-50%,-50%) translateY(${-m*5}px)`;
   cta.style.opacity=still||shown<=g.heroStart?'1':'0';

   // One reversible stride clock from the first houses through the finale.
   // Previously the globe-only progress froze the feet in all later sections.
   pin.style.setProperty('--walking',String(still?0:ease((v.morph-.8)/.2)));
   pin.style.setProperty('--step',Math.sin(shown * Math.PI / 26)*2.1+'px');
   buildings.forEach((el,i)=>{
    const contact=still?0:ease(Math.max(0,1-Math.abs((v.hp-.32)/.46*g.buildings.length-(i+.83))*1.65));
    el.parentElement.style.setProperty('--landed',String(contact));
    const body=el.parentElement.querySelector('.lup-building-body'),doorY=Number(body.dataset.doorY);
    // Enlarge around the doorway: the pin landing point stays fixed.
    body.setAttribute('transform',`translate(82 ${doorY}) scale(${1+contact*.22}) translate(-82 ${-doorY})`);
   });
   marks.forEach((el,i)=>{const at=shown-8*(i+1),q=pose(at,b);el.style.opacity=String(still||(v.p>0&&v.p<1)||at<g.heroStart||q.morph<.95?0:q.opacity*(1-i/14)*.32);el.style.transform=`translate(${q.x+(i%2?3:-3)}px,${q.y+16}px) rotate(${q.rotation}deg)`;});
   const globeQ=clamp((v.p-.14)/.7);
   prints.forEach((el,i)=>{
    const birth=(i+1)/49,birthProgress=.14+birth*.7;
    // Remain fixed on the rotating globe after the foot has touched down.
    const longitude=Math.PI-birth*Math.PI*2.5+(v.p-birthProgress)*Math.PI*2;
    const point=surface(longitude,i%2?.012:-.012),age=globeQ-birth;
    el.style.transform=`translate3d(${(point.x+.5)*b.width}px,${(point.y+.5)*b.height}px,0) translate(-50%,-50%) rotate(${Math.sin(longitude)*20}deg) scale(${Math.max(.12,Math.abs(point.front))},1)`;
    el.style.opacity=String(still?0:ease(age/.018)*ease((point.front-.02)/.18)*.7*(1-ease((age-.3)/.3)));
   });
   const arrival=ease((v.p-.78)/.18);invitation.style.setProperty('--arrival',String(still?1:arrival));invitation.classList.toggle('is-arrived',arrival>.6);
   const cp=clamp((shown-g.catStart)/g.catLength),chosen=Math.min(categories.length-1,Math.floor(cp*categories.length));
   categories.forEach((el,i)=>el.classList.toggle('is-tour-active',!still&&!manualCategory&&cp>0&&cp<1&&i===chosen));
   if(!manualCategory){const selected=categories[chosen];caption.textContent=selected.dataset.description;caption.style.setProperty('--tour-color',selected.style.getPropertyValue('--place-color'));}
   g.stops.forEach((stop,i)=>{const t=clamp(1-Math.abs(shown-stop.t)/160);stop.el.style.setProperty('--dock',String(still?0:ease(t)));notes[i].style.setProperty('--note',String(still?1:ease((shown-stop.t+120)/150)));});
   document.dispatchEvent(new CustomEvent('lup:finale-frame',{detail:{scroll:shown,still}}));
   if(!still&&shown!==scrollY)raf=requestAnimationFrame(draw);
  };
  const schedule=()=>{if(!raf)raf=requestAnimationFrame(draw);};
  addEventListener('scroll',schedule,{passive:true});addEventListener('resize',layout,{passive:true});reduced.addEventListener('change',layout);document.addEventListener('visibilitychange',schedule);new ResizeObserver(layout).observe(main);document.fonts?.ready.then(layout);layout();
 };
 document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
