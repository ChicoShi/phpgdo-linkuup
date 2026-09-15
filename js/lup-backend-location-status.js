/* Optional backend position recording must not lock unrelated forms/tables. */
(() => {
    'use strict';
    const install = () => {
        const maps=window.GDO?.Maps;
        if (!maps || typeof maps.positioningError!=='function') return;
        const de=(document.documentElement.lang||'de').startsWith('de');
        let notice;
        const clear=()=>{notice?.remove();notice=null;};
        const gotPosition=maps.gotPosition;
        maps.gotPosition=function(position){clear();return gotPosition.call(this,position);};
        maps.positioningError=function(){
            // The shared Maps handler chains .then() onto GDO.error(), but the
            // Bootstrap implementation returns void. It creates a second modal
            // and traps normal page scrolling on an optional permission failure.
            if (notice) return;
            notice=document.createElement('div');
            notice.className='lup-location-status';
            notice.setAttribute('role','status');
            const message=document.createElement('span');
            message.textContent=de?'Standort nicht verfügbar. Du kannst diese Seite weiter benutzen.':'Location unavailable. You can continue using this page.';
            const retry=document.createElement('button');retry.type='button';
            retry.textContent=de?'Erneut versuchen':'Retry';
            retry.addEventListener('click',()=>{clear();maps.positioning();});
            const dismiss=document.createElement('button');dismiss.type='button';
            dismiss.textContent=de?'Schließen':'Dismiss';dismiss.addEventListener('click',clear);
            notice.append(message,retry,dismiss);
            (document.getElementById('content-wrap')||document.body).prepend(notice);
        };
    };
    document.readyState==='loading'?document.addEventListener('DOMContentLoaded',install):install();
})();
