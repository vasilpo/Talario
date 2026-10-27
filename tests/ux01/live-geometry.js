'use strict';

// Run against the authorized dev_copy document, never a mock catalog.
// Coordinates and IDs only: no account, title, cookie or storage data.
function auditCatalogGeometry(doc) {
    if (!doc || !doc.location || doc.location.origin !== 'https://talario.ru' || !/^\/dev_copy\/(?:index\.php)?$/.test(doc.location.pathname)) {
        return {pass: false, errors: ['Geometry QA refuses documents outside the dev_copy catalog']};
    }
    var feed = doc.querySelector('.talario-ux01 .feed');
    if (!feed) { return {pass: false, errors: ['UX-01 catalog is not ready']}; }
    function rect(element) {
        var r = element.getBoundingClientRect();
        return {x: r.x, y: r.y, width: r.width, height: r.height, bottom: r.bottom};
    }
    var viewport = doc.documentElement.clientWidth;
    var outer = rect(feed.parentElement);
    var grid = rect(feed);
    var columns = viewport >= 900 ? 2 : 1;
    var expectedWidth = Math.min(1248, outer.width - (columns === 2 ? 48 : 32));
    var cards = Array.from(feed.querySelectorAll('.ut2-gl__item.activity')).map(function(card) {
        return rect(card);
    }).filter(function(card) { return card.width > 0 && card.height > 0; });
    var more = feed.parentElement.querySelector('.ut2-load-more-container');
    var pagination = more && more.getBoundingClientRect().height > 0 ? rect(more) : null;
    var errors = [];
    var firstCard = feed.querySelector('.ut2-gl__item.activity');
    var title = firstCard && firstCard.querySelector('.product-title');
    var photo = firstCard && firstCard.querySelector('.photo-block');
    var info = firstCard && firstCard.querySelector('.activity-info');
    var compact = null;
    if (firstCard && (!title || !photo || !info)) {
        errors.push('Card presentation nodes are missing');
    } else if (firstCard) {
        compact = {
            font: parseFloat(doc.defaultView.getComputedStyle(title).fontSize),
            photo: photo.getBoundingClientRect().width,
            infoMargin: parseFloat(doc.defaultView.getComputedStyle(info).marginTop)
        };
        if (!Number.isFinite(compact.font) || !Number.isFinite(compact.infoMargin) || Math.abs(compact.font - 15) > 0.1 || compact.photo > 100.5 || Math.abs(compact.infoMargin) > 0.5) {
            errors.push('Compact card styles overridden by native cascade');
        }
    }
    if (!cards.length) { errors.push('No visible real cards'); }
    if (doc.documentElement.scrollWidth > viewport + 1) { errors.push('Horizontal document overflow'); }
    if (feed.scrollWidth > feed.clientWidth + 1) { errors.push('Horizontal catalog overflow'); }
    if (Math.abs(grid.width - expectedWidth) > 1) { errors.push('Catalog width is squeezed'); }
    if (Math.abs(grid.x + grid.width / 2 - outer.x - outer.width / 2) > 1) { errors.push('Catalog is not centered'); }
    if (pagination && pagination.y < grid.bottom - 1) { errors.push('Pagination is beside or overlaps cards'); }
    var rows = [];
    for (var i = 0; i < cards.length; i += columns) {
        var row = cards.slice(i, i + columns);
        var first = row[0];
        row.forEach(function(card) {
            if (Math.abs(card.y - first.y) > 1 || Math.abs(card.bottom - first.bottom) > 1) { errors.push('Uneven row at card ' + i); }
            if (Math.abs(card.width - first.width) > 1) { errors.push('Unequal widths at card ' + i); }
            if (card.x < grid.x - 1 || card.x + card.width > grid.x + grid.width + 1) { errors.push('Card outside catalog at ' + i); }
        });
        if (row.length === 2 && row[1].x < first.x + first.width - 1) { errors.push('Columns overlap at card ' + i); }
        if (rows.length && first.y < rows[rows.length - 1].bottom - 1) { errors.push('Rows overlap at card ' + i); }
        rows.push({start: i, x: first.x, end: row[row.length - 1].x + row[row.length - 1].width, y: first.y, bottom: first.bottom, height: first.height});
    }
    return {pass: errors.length === 0, compact: compact, viewport: viewport, scrollWidth: doc.documentElement.scrollWidth, count: cards.length, columns: columns, grid: grid, pagination: pagination, cards: cards, rows: rows, errors: errors};
}
