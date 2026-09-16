/* The finale follows the existing smoothed scroll clock and the same GPS node. */
(() => {
 'use strict';
 const init=()=>{
  const section=document.querySelector('.lup-finale');if(!section)return;
  const stage=section.querySelector('.lup-finale-stage'),globe=section.querySelector('.lup-finale-globe'),street=section.querySelector('.lup-finale-street'),route=street.querySelector('.lup-finale-route'),chat=street.querySelector('.lup-finale-chat'),pulse=street.querySelector('.lup-place-pulse');
  const articles=[...section.querySelectorAll('article')],dots=[...section.querySelectorAll('.lup-finale-progress i')];
  const earth=document.querySelector('.lup-earth-scene'),earthHome=earth?.parentElement;
  const heading=section.querySelector('.lup-finale-heading');
  let carried=false;
  globe.classList.add('lup-foreground-earth');document.body.append(globe);
  const visual=section.querySelector('.lup-finale-visual');
  visual.append(chat);chat.classList.add('lup-cafe-conversation');
  chat.innerHTML='<span>BEISPIEL · CHAT IM CAFÉ</span><b>Aus einem Ort wird ein Gespräch.</b><p class="lup-chat-message">Hallo zusammen!</p><p class="lup-chat-message">Wir sitzen gerade draußen.</p><p class="lup-chat-message">Komm gern dazu.</p>';
  const messages=[...chat.querySelectorAll('.lup-chat-message')];
  const clamp=x=>Math.max(0,Math.min(1,x)),ease=x=>{x=clamp(x);return x*x*(3-2*x);},mix=(a,b,t)=>a+(b-a)*t;
  let top=0,length=1;const measure=()=>{top=section.getBoundingClientRect().top+scrollY-(innerWidth<761?76:80);length=Math.max(1,section.offsetHeight-stage.offsetHeight);};
  new ResizeObserver(measure).observe(section);addEventListener('resize',measure,{passive:true});document.fonts?.ready.then(measure);measure();
  const routeLength=route.getTotalLength(),blocks=[...street.querySelectorAll('.lup-street-block')];
  const visits=blocks.map(block=>{
   const x=Number(block.dataset.centerX),y=Number(block.dataset.centerY);let best=Infinity,at=0;
   for(let n=0;n<=240;n++){const q=route.getPointAtLength(routeLength*n/240),d=(q.x-x)**2+(q.y-y)**2;if(d<best){best=d;at=n/240;}}
   return .32+at*.16;
  });
  document.addEventListener('lup:finale-frame',({detail:{scroll:s,still}})=>{
   const p=clamp((s-top)/length),zoom=ease((p-.07)/.23),reveal=ease((p-.18)/.17),walking=clamp((p-.32)/.16)*(1-ease((p-.9)/.075));
   const inside=ease((p-.49)/.065)*(1-ease((p-.84)/.055));
   // Move the existing globe node, never manufacture a second earth.
   const carryAt=top-innerHeight*.35,shouldCarry=!still&&s>=carryAt;
   if(earth&&shouldCarry!==carried){(shouldCarry?globe:earthHome).append(earth);carried=shouldCarry;}
   // Animate in viewport coordinates; the scrolling section must not move the start point.
   const falling=ease((s-carryAt)/Math.max(1,innerHeight*.35+length*.07));
   globe.style.transform=`scale(${mix(1,earth?.dataset.zoomReady==='true'?5.5:1,zoom)})`;
   heading.style.opacity=String(still?1:ease((p-.26)/.08));
   const viewport=visual.getBoundingClientRect(),diameter=innerWidth<761?210:250;
   globe.style.left=(viewport.left+viewport.width/2-diameter/2)+'px';globe.style.top=mix(-diameter-32,innerHeight*.43-diameter/2,falling)+'px';
   globe.style.opacity=String(carried?1-reveal:0);
   street.style.transform=`scale(${mix(.12,1,reveal)*(1+inside*.65)})`;street.style.opacity=String(reveal*(1-inside));
   route.style.opacity=String(still?0:1-ease((p-.46)/.04));
   blocks.forEach((block,i)=>{
    const visit=visits[i],active=ease(clamp(1-Math.abs(p-visit)/.13));
    const cx=Number(block.dataset.centerX),cy=Number(block.dataset.centerY),size=still?1:1+active*.24;
    block.setAttribute('transform',`translate(${cx} ${cy}) scale(${size}) translate(${-cx} ${-cy})`);
    block.style.opacity=String(still?1:.58+active*.42);
   });
   const chatOpen=ease((p-.55)/.035)*(1-ease((p-.82)/.035));chat.style.opacity=String(still?0:chatOpen);chat.style.transform=`translate(-50%,-50%) translateY(${(1-chatOpen)*14}px) scale(${.97+chatOpen*.03})`;
   messages.forEach((message,i)=>{const show=ease((p-(.58+i*.07))/.035);message.style.opacity=String(show);message.style.transform=`translateY(${(1-show)*10}px)`;});
   pulse.style.opacity='0';

   const index=p<.43?0:p<.62?1:p<.83?2:3;
   articles.forEach((el,i)=>{el.classList.toggle('is-current',i===index);el.style.opacity=String(still?1:i===index?ease((p-.28)/.07):0);el.setAttribute('aria-hidden',String(!still&&i!==index));});
   dots.forEach((el,i)=>el.classList.toggle('is-current',i===index));
   const pin=document.querySelector('.lup-unified-pin'),trail=document.querySelector('.lup-adventure-trail');
   if(trail)trail.style.visibility=!still&&s>=top&&s<=top+length?'hidden':'';
   if(still||s<top||s>top+length||!pin)return;
   // Use the transformed SVG path itself, so street and pin cannot drift apart.
   const point=route.getPointAtLength(routeLength*walking),matrix=route.getScreenCTM();if(!matrix)return;
   const target=new DOMPoint(point.x,point.y).matrixTransform(matrix),r=globe.getBoundingClientRect();
   const join=ease((p-.28)/.08),x=mix(r.left+r.width/2,target.x,join),y=mix(innerHeight*.42,target.y-16,join);
   const exit=ease((p-.975)/.025),main=document.querySelector('main.lup-arrival').getBoundingClientRect(),rail=main.left+Math.max(18,(main.width-1160)/2+14);
   pin.style.transform=`translate3d(${mix(x,rail,exit)-12}px,${mix(y,innerHeight*.55,exit)-16}px,0) scale(${mix(1.3,1,exit)})`;
   pin.style.opacity=String(ease(p/.07)*(1-inside));pin.style.setProperty('--walking',String(1-inside));
  });
 };
 document.readyState==='loading'?document.addEventListener('DOMContentLoaded',init):init();
})();
