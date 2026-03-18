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
(function (_, $) {
let popupObserver;
let imagesObserver;
let imageIndex = 0;
let galleryPopupId = null;
let detachedGallery = {};
let lastReviewUrl;
let reviewId = 0;
let initialJump = true;
function popupObserverCb(target) {
return () => {
initGallery('#' + target.id);
initImageObserver(target)
}
}
function incUrlPageQueryParam(link) {
const url = new Url(link);
const page = url.searchParams.get('page') || 0;
url.searchParams.set('page', +page + 1);
return url.toString();
}
function loadImagesChunk(gallery) {
$.ceAjax('request', gallery.dataset.nextPage, {
method: 'get',
is_ajax: true,
callback({images, next_page}) {
if (next_page) {
gallery.dataset.nextPage = incUrlPageQueryParam(gallery.dataset.nextPage);
} else {
delete gallery.dataset.nextPage;
}
$(gallery).append(images);
imagesObserverObserveLast(gallery);
}
});
}
function imagesObserverCb(gallery) {
return (entries, observer) => {
entries.forEach(entry => {
if (!entry.isIntersecting) return;
observer.unobserve(entry.target);
loadImagesChunk(gallery);
})
}
}
function imagesObserverObserveLast(gallery) {
if (gallery.dataset.nextPage)
imagesObserver.observe(gallery.querySelector('.ty-product-review-thumbnail-link:last-child'));
}
function initImageObserver(dialog) {
const gallery = dialog.querySelector('.ty-product-review-thumbnails-gallery[data-next-page]');
if (!gallery) return;
imagesObserver?.disconnect();
const root = window.matchMedia('(max-width: 768px)').matches ? null : gallery;
imagesObserver = new IntersectionObserver(imagesObserverCb(gallery), {root});
imagesObserverObserveLast(gallery);
}
function initPopupObserver(d) {
popupObserver ??= new MutationObserver(popupObserverCb(d));
popupObserver.observe(d, {attributes: false, childList: true, subtree: false});
}
function setPagingText() {
const el = document.getElementById('ty-product-review-popup-gallery-paging');
el.innerText = _.tr('abt__ut2.product_reviews.photo_n_of')?.replace('[n]', this.currentItem + 1) + ` ${this.itemsAmount}`;
}
function fetchReview(url, callback) {
$.ceAjax('request', url, {
callback,
is_ajax: true,
caching: true,
result_ids: 'ty-product-review-post-wrap,ty-product-review-mini-gallery'
});
}
function afterInit(el) {
setPagingText.call(this);
if (this.itemsAmount > 1) {
setMiniGalleryActive.call(this, el);
setMiniGalleryEvents.call(this, el);
}
}
function initGallery(dialog) {
const owl = $(dialog).find('.ty-product-review-popup-gallery:not(.owl-carousel)')
.owlCarousel({
direction: _.language_direction,
itemsCustom: [[300, 1]],
pagination: false,
navigation: true,
lazyLoad: true,
navigationText: ['<i class="ty-icon-left-open-thin"></i>', '<i class="ty-icon-right-open-thin"></i>'],
addClassActive: true,
rewindNav: false,
afterMove,
afterInit,
beforeInit
})
.data('owlCarousel');
const initialIndex = owl && getInitialImageIndex(owl);
owl?.jumpTo(initialIndex);
}
function beforeInit() {
this.options.lazyLoad && this.$elem.find('img:not(.lazyOwl)').addClass('lazyOwl');
}
function getInitialImageIndex(owl) {
const el = owl.$owlItems.find(`[data-review-id=${reviewId}]`);
const index = el.length ? el.filter(`[data-index=${imageIndex}]`).parent().index() : el.parent().index();
return ~index ? index : 0;
}
function setMiniGalleryActive(el) {
const gallery = el.closest('.ty-product-review-post__gallery').find(`.ty-product-review-popup-mini-gallery`);
if (gallery.length === 0) return;
const index = this.$owlItems[this.currentItem].children[0]?.dataset.index ?? 0;
const item = gallery.children()
.removeClass('active')
.filter(`[data-index="${index}"]`)
.addClass('active');
if (item.length === 0) return;
const {scrollLeft, clientWidth} = gallery.get(0);
const {left} = item.get(0).getBoundingClientRect();
if (clientWidth < left - scrollLeft) {
gallery.get(0).scrollTo({left: clientWidth / 2 + left - scrollLeft, behavior: "smooth"});
} else if (left < 30) {
gallery.get(0).scrollTo({left: Math.max(left - clientWidth / 4, 0), behavior: "smooth"});
}
}
function setMiniGalleryEvents(el) {
const miniGallery = el.closest('.ty-product-review-post__gallery').find('.ty-product-review-popup-mini-gallery');
const reviewId = miniGallery.get(0).dataset.review;
const owl = this;
miniGallery.on('click', '.ty-product-review-popup-mini-gallery-img', function () {
const index = owl.$owlItems.find(`[data-review-id=${reviewId}][data-index=${this.dataset.index}]`)?.parent().index() ?? 0;
owl.goTo(index);
});
}
function afterMove(...args) {
const reviewUrl = this.$owlItems[this.currentItem].children[0]?.dataset?.review;
if (reviewUrl && reviewUrl !== lastReviewUrl && !initialJump) {
fetchReview(reviewUrl, () => {
setMiniGalleryEvents.call(this, ...args)
setMiniGalleryActive.call(this, ...args);
lastReviewUrl = reviewUrl;
});
} else {
initialJump = false;
setMiniGalleryActive.call(this, ...args);
}
setPagingText.call(this, ...args);
}
function registerCloseEvent() {
$.ceEvent('one', 'ce.dialogbeforeclose', function (d) {
const isReview = d.parent().hasClass('ut2-customer_review');
if (isReview) {
popupObserver.disconnect();
popupObserver = null;
detachedGallery = {};
return
}
$.ceEvent('one', 'ce.dialogclose', registerCloseEvent);
})
}
$.ceEvent('on', 'ce.dialogshow', function ([d]) {
const isReview = d.parentNode.classList.contains('ut2-customer_review');
if (!isReview) return;
galleryPopupId = d.id;
initPopupObserver(d);
initGallery(d);
initImageObserver(d);
registerCloseEvent();
$('.ty-content-more', d).ceContentMore();
});
$.ceEvent('on', 'ce.abt__ut2_before_ajax_request', function (args) {
if (galleryPopupId && args[2].result_ids !== galleryPopupId) return;
args[2].caching = true;
if (args[2].obj?.hasClass('ut2-customer_review_popup-link')) {
args[2].pre_processing = function () {
const children = $(`#${galleryPopupId}`).children();
detachedGallery.el = children.clone();
detachedGallery.scroll = children.find('.ty-product-review-thumbnails-gallery').scrollTop();
};
} else if (args[2].obj?.hasClass('abt__ut2_product_reviews_gallery_back_link') && detachedGallery.el) {
args[2].defered_object = $.Deferred();
args[2].defered_object.isAborted = true;
const popup = $(`#${galleryPopupId}`).html(detachedGallery.el);
popup.find('.ty-product-review-thumbnails-gallery').scrollTop(detachedGallery.scroll);
}
});
$(_.doc).on('click', '.ty-product-review-thumbnail-link', function () {
imageIndex = +this.dataset.imageIndex ?? 0;
reviewId = (new URLSearchParams(this.href)).get('product_review_id');
}).on('click', '.ty-product-review-copy-link', async function () {
await navigator.clipboard.writeText(this.dataset.link);
$.ceNotification('show', {
title: '',
message: `<div class="js-ins--notif-content-_sku-copied">${_.tr('abt__ut2.link_copied')}<div>`,
type: 'N',
message_state: 'I'
});
});
$.ceEvent('one', 'ce.commoninit', function () {
if (!window.location.hash) return;
const [_d, product_review_id, product_id] = window.location.hash.match(/^#product_review_(\d+)_(\d+)$/) ?? [];
if (!product_id || !product_review_id) return;
reviewId = product_review_id;
$(`<a></a>`)
.attr('href', `${fn_url('')}/?dispatch=abt__ut2_product_reviews.get_product_reviews_gallery_popup&product_id=${product_id}&product_review_id=${product_review_id}&with_images=0`)
.addClass('cm-dialog-opener cm-dialog-auto-size cm-dialog-destroy-on-close hidden')
.data({
caTargetId: `abt__ut2_product_reviews_gallery_popup_${product_id}`,
caDialogTitle: _.tr('abt__ut2.product_reviews.customer_review'),
caDialogClass: 'ut2-customer_review',
imageIndex: 0,
})
.appendTo('body')
.click()
.remove();
});
$.ceEvent('on', 'ce.commoninit', function (el) {
const [_d, id, postfix] = el.attr('id')?.match(/^(product_review-post_votes_\d+)_(\d+)$/) ?? [];
if (!postfix) return
$(`#${id} a`).each(function (idx) {
$(this).html(el.find(`a:eq(${idx})`).html());
});
});
})(Tygh, Tygh.$);