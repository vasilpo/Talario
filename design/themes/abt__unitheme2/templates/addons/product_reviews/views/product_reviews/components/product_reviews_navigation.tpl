{*
    $total_product_reviews
    $config
    $curl
    $product_reviews_with_images
    $product_reviews_sorting
    $product_reviews_sorting_orders
    $product_reviews_search
    $product_reviews_avail_sorting
    $product_id
*}

{if $total_product_reviews}

    {* TODO *}
    {$product_reviews_with_images = ($_REQUEST.with_images === "1")}
    {* /TODO *}

    <nav class="ty-product-review-reviews-navigation">
        <div class="ty-product-review-reviews-navigation__sorting">
            {$curl=$config.current_url|fn_query_remove:"result_ids":"layout"}
            {include file="common/sorting.tpl"
            sorting=$product_reviews_sorting
            sorting_orders=$product_reviews_sorting_orders
            search=$product_reviews_search
            avail_sorting=$product_reviews_avail_sorting
            ajax_class="cm-ajax"
            pagination_id="product_reviews_list_{$product_id}"
            }
            <div class="ty-sort-dropdown">
                <div class="ut2-sort-label">{__('abt__ut2.product_reviews.filter.rating')}:</div>

                <a id="sw_filter_rating_dropdown" class="ty-sort-dropdown__wrapper cm-combination">
                    <span>{if $selected_rating > 0} {__('abt__ut2.product_reviews.filter.rating.n_stars',[$selected_rating])}{else}{__('abt__ut2.product_reviews.filter.rating.all_ratings')}{/if}</span>
                    <i class="ut2-icon-outline-expand_more"></i>
                </a>

                <div id="filter_rating_dropdown" class="ty-sort-dropdown__content cm-popup-box hidden">
                    <div class="ut2-popup-box-title">
                        {__('abt__ut2.product_reviews.filter.rating')}
                        <div class="cm-external-click ut2-btn-close" data-ca-external-click-id="sw_filter_rating_dropdown">
                            <i class="ut2-icon-baseline-close"></i>
                        </div>
                    </div>
                    <ul>
                        <li class="ty-sort-dropdown__content-item">
                            <a class="ty-sort-dropdown__content-item-a cm-ajax"
                               href="{$curl|fn_link_attach:"&abt__ut2_reviews_rating=0&selected_section=product_reviews"|fn_url}"
                               data-ca-target-id="product_reviews_list_{$product_id}">{__('abt__ut2.product_reviews.filter.rating.all_ratings')}</a>
                        </li>
                        {foreach [1,2,3,4,5] as $stars}
                            <li class="ty-sort-dropdown__content-item">
                                <a class="ty-sort-dropdown__content-item-a  cm-ajax"
                                   href="{$curl|fn_link_attach:"&abt__ut2_reviews_rating={$stars}&selected_section=product_reviews"|fn_url}"
                                   data-ca-target-id="product_reviews_list_{$product_id}"
                                >{__('abt__ut2.product_reviews.filter.rating.n_stars',[$stars])}</a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
        <div class="ty-product-review-reviews-navigation__filters">
            <label class="ty-product-review-reviews-navigation__filter {if $product_reviews_only_buyers}ty-product-review-reviews-navigation__filter--active{/if}">
                <input id="product_review_only_buyers"
                       type="checkbox"
                       name="product_review_only_buyers"
                       {if $product_reviews_only_buyers}checked="checked"{/if}
                       class="cm-external-click ty-product-review-reviews-navigation__filter-checkbox
                    {if $product_reviews_only_buyers}
                        ty-product-review-reviews-navigation__filter-checkbox--active
                    {/if}
                    "
                       data-ca-external-click-id="product_review_only_buyers_link"
                >
                <a id="product_review_only_buyers_link"
                   href="{$curl|fn_link_attach:"only_buyers={if $product_reviews_only_buyers}0{else}1{/if}&selected_section=product_reviews"|fn_url}"
                   class="ty-product-review-reviews-navigation__filter-link cm-ajax"
                   data-ca-target-id="product_reviews_list_{$product_id}"
                   rel="nofollow"
                >
                    {__("abt__ut2.product_reviews.filter.only_buyers")}
                </a>
            </label>

            <label class="ty-product-review-reviews-navigation__filter {if $product_reviews_with_images}ty-product-review-reviews-navigation__filter--active{/if}">
                <input id="product_review_with_images"
                       type="checkbox"
                       name="product_review_with_images"
                       {if $product_reviews_with_images}checked="checked"{/if}
                       class="cm-external-click ty-product-review-reviews-navigation__filter-checkbox
                    {if $product_reviews_with_images}
                        ty-product-review-reviews-navigation__filter-checkbox--active
                    {/if}
                    "
                       data-ca-external-click-id="product_review_with_images_link"
                >
                <a id="product_review_with_images_link"
                   href="{$curl|fn_link_attach:"with_images={if $product_reviews_with_images}0{else}1{/if}&selected_section=product_reviews"|fn_url}"
                   class="ty-product-review-reviews-navigation__filter-link cm-ajax"
                   data-ca-target-id="product_reviews_list_{$product_id}"
                   rel="nofollow"
                >
                    {__("product_reviews.with_photo")}
                </a>
            </label>
        </div>
        <div class="ty-product-review-reviews-navigation__total">{__('abt__ut2.product_reviews.we_found_n_reviews', [$abt__ut2_product_reviews_count])}</div>
    </nav>
{/if}
