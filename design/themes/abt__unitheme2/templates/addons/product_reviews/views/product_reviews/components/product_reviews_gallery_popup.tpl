<div id="abt__ut2_product_reviews_gallery_popup_{$product_id}">

    {$selected_period = $_REQUEST.abt__ut2_reviews_period|default:'all'}
    {$selected_rating = $_REQUEST.abt__ut2_reviews_rating|default:0}
    {$product_reviews_only_buyers = ($_REQUEST.only_buyers === "1")}

    {$curl = fn_url("abt__ut2_product_reviews.get_product_reviews_gallery_popup&product_id=`$product_id`&abt__ut2_reviews_period=`$selected_period`&abt__ut2_reviews_rating=`$selected_rating`&only_buyers={$product_reviews_only_buyers}")}
    {$images_url= fn_url("abt__ut2_product_reviews.get_product_reviews_gallery_popup_page&product_id=`$product_id`&abt__ut2_reviews_period=`$selected_period`&abt__ut2_reviews_rating=`$selected_rating`&only_buyers={$product_reviews_only_buyers}")}
    {if isset($product_reviews) }

        {include file="common/pagination.tpl" id="abt__ut2_product_reviews_popup_pagination_{$product_id}" search=$search}

        <div id="abt__ut2_product_reviews_gallery_popup_{$product_id}" class="abt__ut2_product_reviews_gallery_popup_content">
            <nav class="ty-product-review-reviews-navigation">
                <div class="ty-product-review-reviews-navigation__sorting">
                    {include file="common/sorting.tpl"
                    sorting=$product_reviews_sorting
                    sorting_orders=$product_reviews_sorting_orders
                    search=$search
                    avail_sorting=$product_reviews_avail_sorting
                    ajax_class="cm-ajax"
                    pagination_id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
                    element="elm_sorting_gallery_popup"
                    }
                    <div class="ty-sort-dropdown">
                        <div class="ut2-sort-label">{__('abt__ut2.product_reviews.filter.rating')}:</div>

                        <a id="sw_popup_filter_rating_dropdown" class="ty-sort-dropdown__wrapper cm-combination">
                            <span>{if $selected_rating > 0}{__('abt__ut2.product_reviews.filter.rating.n_stars',[$selected_rating])}{else}{__('abt__ut2.product_reviews.filter.rating.all_ratings')}{/if}</span>
                            <i class="ut2-icon-outline-expand_more"></i>
                        </a>

                        <div id="popup_filter_rating_dropdown"
                             class="ty-sort-dropdown__content cm-smart-position cm-popup-box hidden">
                            <div class="ut2-popup-box-title">
                                {__('abt__ut2.product_reviews.filter.rating')}
                                <div class="cm-external-click ut2-btn-close"
                                     data-ca-external-click-id="sw_popup_filter_rating_dropdown">
                                    <i class="ut2-icon-baseline-close"></i>
                                </div>
                            </div>
                            <ul>
                                <li class="ty-sort-dropdown__content-item">
                                    <a class="ty-sort-dropdown__content-item-a cm-ajax"
                                       href="{$curl|fn_link_attach:"abt__ut2_reviews_rating=0"|fn_url}"
                                       data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}">{__('abt__ut2.product_reviews.filter.rating.all_ratings')}</a>
                                </li>
                                {foreach [1,2,3,4,5] as $stars}
                                    <li class="ty-sort-dropdown__content-item">
                                        <a class="ty-sort-dropdown__content-item-a  cm-ajax"
                                           href="{$curl|fn_link_attach:"abt__ut2_reviews_rating={$stars}"|fn_url}"
                                           data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
                                        >{__('abt__ut2.product_reviews.filter.rating.n_stars',[$stars])}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ty-product-review-reviews-navigation__filters">
                <label class="ty-product-review-reviews-navigation__filter {if $product_reviews_only_buyers}ty-product-review-reviews-navigation__filter--active{/if}">
                    <input id="popup_product_review_only_buyers"
                           type="checkbox"
                           name="product_review_only_buyers"
                           {if $product_reviews_only_buyers}checked="checked"{/if}
                           class="cm-external-click ty-product-review-reviews-navigation__filter-checkbox{($product_reviews_only_buyers)?" ty-product-review-reviews-navigation__filter-checkbox--active":""}"
                           data-ca-external-click-id="popup_product_review_only_buyers_link"
                    >
                    <a id="popup_product_review_only_buyers_link"
                       href="{$curl|fn_link_attach:"only_buyers={if $product_reviews_only_buyers}0{else}1{/if}"|fn_url}"
                       class="ty-product-review-reviews-navigation__filter-link cm-ajax"
                       data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
                       rel="nofollow"
                    >
                        {__("abt__ut2.product_reviews.filter.only_buyers")}
                    </a>
                </label>
                </div>
                <div class="ty-product-review-reviews-navigation__total ty-muted">{__("abt__ut2.product_reviews.matching_photos_found",[$search.total_items])}</div>
            </nav>

            <div class="ty-product-review-thumbnails-gallery ty-mt-m" {if $next_page}data-next-page="{$images_url|fn_link_attach:"page=`$next_page`"}"{/if}>
                {if !$product_reviews}
                    <div class="ty-no-items" style="grid-column: 1 / -1" >{__("product_reviews.no_reviews_found")}</div>
                {/if}
                {include file="addons/product_reviews/views/product_reviews/components/post_popup_images.tpl"}
            </div>
        </div>
    {elseif $product_review}
        {$is_few_img = $product_review.images|count > 1}
        <div class="ty-product-review-post__container">
        {if $product_review.images|count}
            <a data-ca-target-id="abt__ut2_product_reviews_gallery_popup_{$product_id}"
               class="cm-ajax ty-inline-block ty-mb-s abt__ut2_product_reviews_gallery_back_link"
               href="{$curl|fn_query_remove:"product_review_id"}"
               style="display: flex; align-items: center;"
               rel="nofollow">
                <i class="ut2-icon-arrow_back_black ty-valign"></i><span>{__("abt__ut2.product_reviews.all_photos")}</span>
            </a>
        {else}
            <a
               href="javascript:void(0)"
               class="ty-inline-block ty-mb-s cm-scroll cm-external-click cm-dialog-closer"
               data-ca-scroll="#product_reviews"
               style="display: flex; align-items: center;"
               >
                <i class="ut2-icon-arrow_back_black ty-valign"></i><span>{__("abt__ut2.product_reviews.all_reviews")}</span>
            </a>
        {/if}
        <div class="ty-product-review-post__gallery">
            {if $product_review.images|count}
            {strip}
                <div>
                    <div id="ty-product-review-gallery-{$product_review.product_review_id}"
                         class="ty-product-review-popup-gallery"
                         style="{if $is_few_img}opacity: 0;{/if}"
                    >
                        {foreach from=$reviews_images  key=rid item=images}
                            {$link_current=fn_link_attach($curl,"product_review_id=`$rid`")}
                            {foreach $images as $image}
                                {$thumb_h= ($image.detailed.image_y > $image.detailed.image_x)?490:0}
                                {$thumb_w= ($image.detailed.image_y <= $image.detailed.image_x)?490:0}
                                <div class="ty-product-review-popup-gallery-img"
                                     data-review-id="{$rid}"
                                     data-review="{$link_current}"
                                     data-index="{$image@index}"
                                >
                                    {include file="common/image.tpl" images=$image image_width=$thumb_w image_height=$thumb_h no_ids=true lazy_load=true}
                                </div>
                            {/foreach}
                        {/foreach}
                    </div>
                    <div id="ty-product-review-popup-gallery-paging" class="ty-center"></div>
                    {if $settings.ab__device == "mobile"}
                      {include file="addons/product_reviews/views/product_reviews/components/post_mini_gallery.tpl"}
                    {/if}
                </div>
            {/strip}
            {/if}
            {$sw_id_postfix=($is_most_helpful)?'_02':'_2'}
            <div class="ty-product-review-post-wrap" id="ty-product-review-post-wrap">
                <article class="ty-product-review-post">
                    {include file="addons/product_reviews/views/product_reviews/components/post_customer.tpl"
                    product_review=$product_review
                    }

                    <section class="ty-product-review-post__content ty-dialog-caret">
                        {include file="addons/product_reviews/views/product_reviews/components/post_header.tpl"
                        product_review=$product_review
                        }

                        {include file="addons/product_reviews/views/product_reviews/components/post_message.tpl"
                        product_review=$product_review
                        }

                        {include file="addons/product_reviews/views/product_reviews/components/post_vendor_reply.tpl"
                        product_review=$product_review
                        }

                        {include file="addons/product_reviews/views/product_reviews/components/post_footer.tpl"
                        product_review=$product_review
                        no_images=($settings.ab__device === "mobile")
                        mini_gallery=($settings.ab__device != "mobile")
                        sw_id_postfix=$sw_id_postfix
                        }
                    </section>
                </article>
                <!--ty-product-review-post-wrap--></div>
            <script>
                $.ceEvent('on', "ce.switch_copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}" , function(flag){
                    !flag && document.getElementById("copy_review_link_{$product_review.product_review_id}_{$product_review.product_id}_{$sw_id_postfix}")?.scrollIntoView();
                });
            </script>
        </div>
        </div>
    {/if}
<!--abt__ut2_product_reviews_gallery_popup_{$product_id}--></div>