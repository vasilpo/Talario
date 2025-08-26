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
let THUMBNAILS_GALLERY_FORMAT = _.abt__ut2.settings.products.view.thumbnails_gallery_format[_.abt__ut2.device];
$.ceEvent('on', 'ce.product_image_gallery.ready', function(){
if(THUMBNAILS_GALLERY_FORMAT === 'lines_only'){
function updateCounter () {
let activeElem = this.counterElem.querySelector('.active');
if(activeElem){
activeElem.classList.remove('active');
}
let currentItemPosition = this.currentItem+1;
this.counterElem.querySelector('*:nth-child('+currentItemPosition+')').classList.add('active');
}
}else{
function updateCounter () {
this.counterElem.innerHTML = Number(this.currentItem+1) + ' ' + _.tr('abt__ut2_of') + ' ' + Number(this.itemsAmount);
}
}
let changeMoveAction = () => {
let owlInstance = $('.ut2-pb__img-wrapper:not(.quick-view) .cm-preview-wrapper').data('owl-carousel');
if(owlInstance !== undefined && owlInstance.itemsAmount > 1){
let counterElem = _.doc.createElement('div');
counterElem.className = 'abt__ut2_pig_counter';
if(THUMBNAILS_GALLERY_FORMAT === 'counter_only'){
counterElem.className = 'abt__ut2_pig_counter counter';
}
if(THUMBNAILS_GALLERY_FORMAT === 'lines_only'){
counterElem.className = 'abt__ut2_pig_counter dotes';
counterElem.addEventListener('click',function(e){
if(!e.target.classList.contains('abt__ut2_pig_counter')){
let element = e.target;
owlInstance.goTo(Array.from(element.parentNode.children).indexOf(element));
}
}
);
let lineElem = _.doc.createElement('div');
for (var i = 0; i < owlInstance.itemsAmount; i++){
counterElem.append(lineElem.cloneNode());
}
}
owlInstance.counterElem = counterElem;
owlInstance.wrapperOuter.append(counterElem)
updateCounter.bind(owlInstance)();
owlInstance.options.afterMove = updateCounter;
}
};
setTimeout(changeMoveAction,0);
});
}(Tygh, Tygh.$));