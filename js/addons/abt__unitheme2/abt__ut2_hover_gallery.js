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
(function(_, $) {
$(_.doc).on({
mouseenter:changeMainImage,
mouseleave:restoreMainImage
},'.cm-ab-hover-gallery div');
let getImage = (el) => {
return el.closest('.ut2-gl__image, .ut2-pl__image').querySelector('img.img-ab-hover-gallery');
}
let setActiveIndicator = (elem, first = false) => {
let container = elem.closest('.cm-ab-hover-gallery')
,indicatorsContainer = container.querySelector('.abt__ut2_hover_gallery_indicators');
let index = 0;
if(!first){
index = Array.from(container.children).indexOf(elem);
}
Array.from(indicatorsContainer.children).forEach((item) => {
item.classList.remove('active');
});
indicatorsContainer.children.item(index)?.classList.add('active');
}
function changeMainImage (ev){
let elem = ev.target
,img = getImage(elem)
setActiveIndicator(elem);
if(img && elem.dataset['caProductAdditionalImageSrc'] !== undefined){
['src','srcset'].map(function (e){
let ce = e.charAt(0).toUpperCase() + e.slice(1);
if (img[e] !== '' && elem.dataset['caProductAdditionalImage'+ce] !== ''){
img.dataset[e+'_old'] = img[e];
img[e] = elem.dataset['caProductAdditionalImage'+ce];
}
});
}
}
function restoreMainImage (ev){
let elem = ev.target
,img = getImage(elem)
setActiveIndicator(elem,true);
if(img ) {
['src','srcset'].map(function (e){
if(img.dataset[e+'_old'] !== undefined){
img[e] = img.dataset[e+'_old'];
}
})
}
}
}(Tygh, Tygh.$));