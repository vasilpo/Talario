/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
window.addEventListener('pageshow', function (event) {
if (!event.persisted) return;
const sockets = Array.from(window._websockets || []);
sockets.forEach(([closedWs, params = {}]) => {
const { url, protocol } = closedWs;
const ws = new WebSocket(url, protocol || undefined);
Object.entries(params).forEach(([event, listeners]) => {
listeners.forEach(func => {
ws.addEventListener(event, func);
});
});
window._websockets?.delete(closedWs);
})
$.ceAjax('request', fn_url('index.index.check_changes'), {
method: 'get',
result_ids: 'cart_status_*,abt__ut2_wishlist_count,abt__ut2_compared_products',
full_render: true,
hidden: true,
});
});
window.addEventListener('pagehide', function (event) {
window._websockets?.forEach((_, ws) => {
ws.readyState < 2 && ws.close(4444);
})
});
