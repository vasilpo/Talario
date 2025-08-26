
{if $details_page || $quick_view}

    {capture name='image_previewer_template'}
        {capture name='image_previewer_hook_data'}
            {hook name="products:ab__image_previewer"}
            {/hook}
        {/capture}
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="pswp__bg"></div>
            <div class="pswp__scroll-wrap">
                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>
                <div class="pswp__ui pswp__ui--hidden">
                    <div class="pswp__top-bar">
                        <div class="pswp__title">{$product.product nofilter}</div>
                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <button class="pswp__button pswp__button--share" title="Share"></button>
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                                <div class="pswp__preloader__cut">
                                    <div class="pswp__preloader__donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div>
                    </div>
                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                    {if $addons.ab__image_previewers.ps_display_navigation_dots == 'Y'}
                        <div class="pswp__counter"></div>
                        <div class="pswp__dots">
                        </div>
                    {/if}
                    <div class="pswp__caption {if $smarty.capture.image_previewer_hook_data|trim}avail{/if}">
                        <div class="pswp__caption__center">
                            {$smarty.capture.image_previewer_hook_data nofilter}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/capture}
    <template id="ab__image_previewer_template_{"preview[product_images_`$preview_id`]"}">
        {$smarty.capture.image_previewer_template nofilter}
    </template>
{/if}

