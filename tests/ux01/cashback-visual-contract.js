'use strict';

// Read-only browser assertion for the actual authorized dev_copy catalog.
// Node syntax checking is not a visual PASS; execute at both required viewports.
function auditCashbackSurface(doc) {
    if (!doc || !doc.location || doc.location.origin !== 'https://talario.ru' || !/^\/dev_copy\/(?:index\.php)?$/.test(doc.location.pathname)) {
        return {pass: false, errors: ['Cashback QA refuses documents outside the dev_copy catalog']};
    }
    var win = doc.defaultView;
    var footers = Array.from(doc.querySelectorAll('#talario-ux01 .card-foot.ux01-has-cashback')).filter(function (foot) {
        return foot.getBoundingClientRect().width > 0 && foot.getBoundingClientRect().height > 0;
    });
    var errors = [], surfaces = [];
    if (!footers.length) errors.push('No visible priced cashback footer: visual contract is unverified');
    footers.forEach(function (foot, index) {
        var badges = foot.querySelectorAll('.ux01-cashback');
        if (badges.length !== 1) { errors.push('Expected one cashback surface at footer ' + index); return; }
        var badge = badges[0], style = win.getComputedStyle(badge), box = badge.getBoundingClientRect();
        var background = style.backgroundColor;
        var transparent = background === 'transparent' || /^rgba\([^)]*,\s*0(?:\.0+)?\)$/.test(background) || /\/\s*0(?:\.0+)?\)$/.test(background);
        var borders = [style.borderTopWidth, style.borderRightWidth, style.borderBottomWidth, style.borderLeftWidth];
        var radii = [style.borderTopLeftRadius, style.borderTopRightRadius, style.borderBottomRightRadius, style.borderBottomLeftRadius];
        if (badge.parentElement !== foot) errors.push('Cashback is not a direct footer peer at ' + index);
        if (box.width <= 0 || box.height <= 0) errors.push('Cashback is not visible at ' + index);
        if (!transparent || style.backgroundImage !== 'none') errors.push('Cashback has a painted background at ' + index);
        if (!borders.every(function (value) { return parseFloat(value) === 0; })) errors.push('Cashback has a capsule border/divider at ' + index);
        if (!radii.every(function (value) { return value.split(/\s+/).every(function (part) { return parseFloat(part) === 0; }); })) errors.push('Cashback has rounded capsule corners at ' + index);
        if (style.boxShadow !== 'none') errors.push('Cashback has a capsule shadow at ' + index);
        surfaces.push({index: index, background: background, image: style.backgroundImage, borders: borders, radii: radii, shadow: style.boxShadow});
    });
    return {pass: errors.length === 0, viewport: win.innerWidth, count: surfaces.length, surfaces: surfaces, errors: errors};
}
