'use strict';

// Runs against the real dev_copy document. Source/CI checks are not a visual PASS.
function auditVariant3(root) {
    var doc = root.ownerDocument, win = doc.defaultView, errors = [], cards = [];
    if (doc.location.origin !== 'https://talario.ru' || !/^\/dev_copy\/(?:index\.php)?$/.test(doc.location.pathname)) {
        return {pass:false, errors:['Variant3 QA is restricted to the dev_copy catalog']};
    }
    function box(el) {
        if (!el) return null;
        var r = el.getBoundingClientRect();
        return {x:r.x,y:r.y,right:r.right,bottom:r.bottom,width:r.width,height:r.height};
    }
    var mobile = win.innerWidth < 900;
    Array.from(doc.querySelectorAll('#talario-ux01 .activity')).forEach(function (card, index) {
        var foot = card.querySelector('.card-foot'), price = foot && foot.querySelector('.price-stack');
        var more = foot && foot.querySelector('a.more'), cash = foot && foot.querySelector('.ux01-cashback');
        var f = box(foot), p = box(price), a = box(more), c = box(cash), r = box(card);
        var local = [], amount = null, raw = price && price.querySelector('.ty-price-num');
        if (raw) amount = Number(raw.textContent.replace(/[^\d.,]/g,'').replace(',','.'));
        var trial = amount === 0 && /ПРОБНОЕ/i.test(price.textContent);
        var soon = !!price && /Скоро на Таларио/.test(price.textContent);
        var value = cash && cash.querySelector('.ux01-cashback-value');
        if (!f || !p || !a || r.width < 100 || r.height < 44) local.push('missing or collapsed native card/footer');
        if (f && p && a) {
            if (a.x < f.x-1 || a.right > f.right+1 || a.bottom > f.bottom+1) local.push('Details leaves its footer');
            if (p.x < f.x-1 || p.right > f.right+1) local.push('price leaves its footer');
            if (mobile) {
                if (a.y < p.bottom-1 || (c && a.y < c.bottom-1)) local.push('Details is not below price and reward');
                if (a.width < f.width-24 || a.height < 42 || a.height > 44) local.push('mobile Details must be full-width and a uniform42px high');
            } else {
                if (Math.abs(a.width-118)>1 || Math.abs(a.height-56)>1) local.push('desktop Details must be uniform118x56');
                if (Math.abs(a.right-f.right)>1) local.push('desktop Details must share the footer right edge');
                if (c && (c.right>a.x+1 || a.x-c.right>16)) local.push('reward is not adjacent to Details');
            }
            if (c && p.right>c.x+1) local.push('price overlaps reward');
            if (c && Math.abs((p.y+p.height/2)-(c.y+c.height/2))>5) local.push('price and reward do not share their first row');
        }
        if ((trial || soon) && cash) local.push('free/unavailable card promises cashback');
        if (cash) {
            var s=win.getComputedStyle(cash);
            if (!value || !/^\+[\d\s\u00a0\u202f]+$/.test(value.textContent)) local.push('reward is not compact+N');
            if (cash.querySelector('.ux01-cashback-lines,.ux01-cashback-meta')) local.push('legacy cashback wording is still in the card');
            if (amount===0) local.push('zero cashback must not appear');
            if (amount>0 && value && value.textContent.replace(/\s/g,'')!=='+'+Math.round(amount*.03)) local.push('native amount and3% reward disagree');
            if (s.backgroundColor!=='transparent'&&!/^rgba\([^)]*,\s*0\)$/.test(s.backgroundColor)) local.push('reward has painted background');
            if ([s.borderTopWidth,s.borderRightWidth,s.borderBottomWidth,s.borderLeftWidth].some(function(v){return parseFloat(v)!==0;})) local.push('reward has a border');
            if (s.boxShadow!=='none') local.push('reward has a shadow');
        }
        if (amount>0 && !soon && !cash) local.push('real paid card has no reward');
        local.forEach(function(e){errors.push('card'+index+': '+e);});
        cards.push({index:index,amount:amount,trial:trial,soon:soon,value:value&&value.textContent,card:r,footer:f,price:p,cashback:c,more:a,errors:local});
    });
    var mandatory={freeTrial:cards.some(function(c){return c.trial;}),paid1000:cards.some(function(c){return c.amount===1000&&c.value==='+30';}),paid1500:cards.some(function(c){return c.amount===1500&&c.value==='+45';}),unavailable:cards.some(function(c){return c.soon;})};
    Object.keys(mandatory).forEach(function(k){if(!mandatory[k])errors.push('Missing real sample: '+k);});
    var profile=doc.querySelector('#talario-ux01 .header-actions .user-link'), auth=root.classList.contains('is-auth');
    var balance=profile&&profile.querySelector('.ux01-header-balance'), avatar=profile&&Array.from(profile.children).filter(function(n){return n.tagName.toLowerCase()==='svg';});
    var header={authenticated:auth,avatarCount:avatar?avatar.length:0,balance:balance&&balance.textContent,balanceBox:box(balance),profileBox:box(profile),href:profile&&profile.getAttribute('href'),welcomeNodes:doc.querySelectorAll('.desktop-bonus,.mobile-bonus').length};
    if (!profile) errors.push('Missing account link');
    if (auth && (header.avatarCount!==0 || !balance || !header.balanceBox.width || !profile.querySelector('.header-user-name'))) errors.push('Authenticated name/coins/balance header is incomplete or contains redundant avatar');
    if (auth && header.welcomeNodes!==0) errors.push('Authenticated user sees guest welcome');
    if (!auth && (balance || header.avatarCount!==1 || !/\/login\//.test(header.href||''))) errors.push('Guest login/balance separation failed');
    var active=[];
    Array.from(doc.querySelectorAll('.ux01-cashback-tip[open]')).forEach(function(tip){
        var help=tip.querySelector('.ux01-cashback-help'),r=box(help);
        var hits=[[12,10],[r.width-12,10],[12,r.height-10],[r.width-12,r.height-10]].map(function(p){return help.contains(doc.elementFromPoint(r.x+p[0],r.y+p[1]));});
        var visible=r.width>0&&r.height>0&&win.getComputedStyle(help).visibility==='visible';
        var rootStyle=win.getComputedStyle(root);
        var inset=Math.max(parseFloat(rootStyle.getPropertyValue('--ux01-consent-inset'))||0,parseFloat(rootStyle.getPropertyValue('--bp-bottom-panel-height'))||0);
        var within=r.x>=12&&r.right<=root.clientWidth-12&&r.y>=0&&r.bottom<=win.innerHeight-inset-12;
        if(!visible||!within||!hits.every(Boolean))errors.push('Active cashback help is clipped, covered or outside viewport');
        var summary=box(tip.querySelector('summary')),corridor=null;
        var summaryHit=tip.querySelector('summary').contains(doc.elementFromPoint(summary.x+summary.width/2,summary.y+summary.height/2));
        if(!summaryHit)errors.push('Active cashback summary is covered and cannot receive its native click');
        var above=r.bottom<summary.y,below=r.y>summary.bottom;
        if(above||below){
            var start=above?summary.y+1:summary.bottom-1,end=above?r.bottom-1:r.y+1;
            var steps=Math.ceil(Math.abs(end-start)*2),failed=[];
            for(var step=0;step<=steps;step++){
                var y=start+(end-start)*step/steps,x=summary.x+summary.width/2;
                if(!tip.contains(doc.elementFromPoint(x,y)))failed.push({x:x,y:y});
            }
            corridor={pass:failed.length===0,samples:steps+1,failed:failed};
            if(!mobile&&!corridor.pass)errors.push('Desktop pointer loses the tooltip between its summary and offer link');
        }
        active.push({box:r,hits:hits,within:within,visible:visible,summaryHit:summaryHit,hoverCorridor:corridor,text:help.innerText});
    });
    if(root.scrollWidth>root.clientWidth)errors.push('Horizontal storefront overflow');
    return {pass:errors.length===0,scope:'actual CS-Cart storefront; responsive desktop UA',viewport:win.innerWidth,client:root.clientWidth,scroll:root.scrollWidth,mandatory:mandatory,header:header,cards:cards,activeTooltips:active,errors:errors};
}
if (typeof module==='object' && module.exports) module.exports={auditVariant3:auditVariant3};
