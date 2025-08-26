/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2023   *
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
/* previewer-description:ab__photoswipe */
(function (_, $) {

    function fn_display(elm) {
        if(window.is_ps_initing === true) return;
        window.is_ps_initing = true;
        var imageId = elm.data('caImageId');
        var elms = $('a[data-ca-image-id="' + imageId + '"] img');
        var imgs = [];
        elms.each(function (i, e) {
            let image_href = $(e).parent('a').attr('href') || $(e).attr('src');
            // let parsed_sizes = image_href.match(/\/thumbnails\/(\d*)\/(\d*)\//);
            // let parsed_width = parsed_sizes !== null && parsed_sizes[1] !== undefined ? parsed_sizes[1] : false
            // let parsed_height = parsed_sizes !== null && parsed_sizes[2] !== undefined ? parsed_sizes[2] : false
            let image_data = {
                'src': image_href,
                'msrc': image_href,
                'w': 0,
                'h': 0,
            }
            imgs.push(image_data);
        });

        var activeIndexImg = elm.data('caImageOrder') || $(elm).closest(".owl-item.active").index();
        if (activeIndexImg === -1) {
            activeIndexImg = 0;
        }
        if(_.language_direction === 'rtl'){
            imgs = imgs.reverse();
            activeIndexImg = Math.abs(activeIndexImg - imgs.length) - 1;
        }

        var image_previewer_template = document.getElementById('ab__image_previewer_template_' + imageId);
        var previewer_body = image_previewer_template ? $(image_previewer_template.content) : $('<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><button class="pswp__button pswp__button--close" title="Close (Esc)"></button><button class="pswp__button pswp__button--share" title="Share"></button><button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button><button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"> </button><button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"> </button><div class="pswp__counter"></div><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>');
        var cloned_previewer_body = previewer_body.clone();
        cloned_previewer_body.appendTo(_.body);

        var pswpElement = document.getElementsByClassName('pswp')[0];
        var options = {
            isRtl : _.language_direction === 'rtl',
            history: false,
            closeOnScroll: false,

             pinchToClose: false,
             closeOnVerticalDrag: false,

            hideAnimationDuration:150,
            showHideOpacity:true,
            getThumbBoundsFn: function(index) {
                var thumbnail = elm.find('img')[0];
                var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
                var rect = thumbnail.getBoundingClientRect();
                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
            },

            index: activeIndexImg,
            shareEl: false,
            counterEl: false,
            clickToCloseNonZoomable: false,
            addCaptionHTMLFn: function (item, captionEl, isFake) {
                if (captionEl.classList.contains('avail')) {
                    //Чтобы отрисовать капчу нужно установить фиктивный елемент
                    item.title = true;
                    return true;
                }
                return false;
            },
        };
        if (_.ab__ip_ps_settings !== undefined) {
            options.zoomEl = _.ab__ip_ps_settings.display_zoom || false;
            options.fullscreenEl = _.ab__ip_ps_settings.display_fullscreen || false;
            options.closeOnVerticalDrag = options.pinchToClose = !!_.ab__ip_ps_settings.close_with_gesture;
        }
        var dots = $(pswpElement).find('.pswp__dots');
        var buttons = $(pswpElement).find('.pswp__button_external');


        if (dots.length) {
            let dot_size = 30;
            // Display counter instead of dots if its too match dots for this screen
            if(imgs.length * dot_size > window.innerWidth){
                options.counterEl = true;
            }else{
                var dots_container = dots[0];
                var dot_elem = document.createElement('div');
                dot_elem.className = 'pswp__dot_elem';
                for (var i = 0; i < imgs.length; i++) {
                    var cloned_dot_elem = dot_elem.cloneNode(true);
                    if (i === activeIndexImg) {
                        cloned_dot_elem.classList.add('active');
                    }
                    dots_container.appendChild(cloned_dot_elem);
                }
            }
        }
        var photoSwipeInst = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, imgs, options);

        photoSwipeInst.listen('gettingData', function(index, item) {
            if (item.w < 1 || item.h < 1) { // unknown size
                var img = new Image();
                img.onload = function() { // will get size after load
                    item.w = this.width; // set image width
                    item.h = this.height; // set image height
                    photoSwipeInst.invalidateCurrItems(); // reinit Items
                    photoSwipeInst.updateSize(true); // reinit Items
                }
                img.src = item.src; // let's download image
            }
        });
        photoSwipeInst.init();
        //Обработка нажатий на кастомные кнопки с закрытием окна просмотра
        if (buttons.length) {
            buttons.each(function (key, item) {
                if ($(item).data('caExternalClickId') !== undefined) {
                    item.addEventListener('click', function () {
                        $('#' + $(item).data('caExternalClickId')).click();
                        photoSwipeInst.close();
                    })
                }
            })
        }
        photoSwipeInst.listen('destroy', function () {
            let fsApi = photoSwipeInst.ui.getFullscreenAPI();
                if(fsApi.isFullscreen && fsApi.isFullscreen()){
                    fsApi.exit().then(()=>{
                        pswpElement.remove();
                    });
                }else{
                    pswpElement.remove();
                }
            window.is_ps_initing = false;
        });

        //Навигационные точки
        if (typeof dots_container !== 'undefined') {
            photoSwipeInst.listen('beforeChange', function (index, item) {
                $(dots_container).find('.active').removeClass('active');
                $(dots_container).children().eq(this.getCurrentIndex()).addClass('active');
            });

            dots_container.addEventListener('click', function (e, t) {
                if($(e.target).hasClass('pswp__dot_elem')){
                    photoSwipeInst.goTo($(dots_container).children().index($(e.target)), $(e.target));
                }
            })
        }
        photoSwipeInst.bg.style.backgroundColor = '#fff';

    }

    $.cePreviewer('handlers', {
        display: function (elm) {
            if (typeof PhotoSwipe === "undefined") {
                $.loadCss(['design/themes/responsive/css/addons/ab__image_previewers/photoswipe/photoswipe.css']);
                $.getScript('js/addons/ab__image_previewers/lib/photoswipe/photoswipe.min.js', function () {
                    // $.loadCss(['design/themes/responsive/css/addons/ab__image_previewers/photoswipe/default-skin.css']);
                    $.getScript('js/addons/ab__image_previewers/lib/photoswipe/photoswipe-ui-default.min.js', function () {
                        fn_display(elm)
                    });
                });
            } else {
                fn_display(elm);
            }
        }
    });
}(Tygh, Tygh.$));
