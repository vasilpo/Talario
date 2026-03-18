{*
    $container_id
    $wrap
    $title
    $subheader
    $product_id
    $product_reviews
    $product_reviews_search
    $locate_to_product_review_tab
*}

{$selected_period = $_REQUEST.abt__ut2_reviews_period|default:'all'}
{$selected_rating = $_REQUEST.abt__ut2_reviews_rating|default:0}
{$product_reviews_only_buyers = ($_REQUEST.only_buyers === "1")}

{$curl=$config.current_url}

<div class="ty-product-reviews-view" id="{if $container_id}{$container_id}{else}content_product_reviews{/if}">
    {if $wrap == true}
        {capture name="content"}
        {include file="common/subheader.tpl" title=$title}
    {/if}

    {if $subheader}
        <h4>{$subheader}</h4>
    {/if}
    <section class="ty-product-reviews-view__main {if $product.product_reviews_count == 0}no-reviews{/if}">
        <div class="ty-product-reviews-view__main-content" id="product_reviews_list_{$product_id}">
            {$product = fn_abt__ut2_get_product_rating($product)}
            <div class="ty-product-reviews-view__main-content-left">
                {include file="addons/product_reviews/views/product_reviews/components/product_rating.tpl"
                ratings_stats=$product.product_reviews_rating_stats.ratings
                average_rating=fn_abt__ut2_get_average_product_rating($product)
                total_product_reviews=$product.product_reviews_rating_stats.total
                }
                {if $product.product_reviews_count > 0 && $settings.ab__device !== 'mobile'}
                    {include file="addons/product_reviews/views/product_reviews/components/product_reviews_sidebar.tpl"
                    product=$product
                    product_id=$product_id
                    locate_to_product_review_tab=$locate_to_product_review_tab
                    product_reviews=$product_reviews
                    }
                {elseif $product.product_reviews_count > 0 && $settings.ab__device == 'mobile' }
                    {include file="addons/product_reviews/views/product_reviews/components/write_product_review.tpl"
                    name=__("product_reviews.write_review")
                    product_id=$product_id
                    locate_to_product_review_tab=$locate_to_product_review_tab
                    }
                {/if}
                {if $settings.abt__ut2.product_reviews.filter_by_creation_time[$settings.ab__device] == 'Y' && $product.product_reviews_count > 0}
                    <div class="ty-tabs">
                        <ul class="ty-tabs__list ut2-scroll-content">
                            {foreach fn_abt__ut2_get_reviews_base_periods() as $key => $period}
                                <li class="ty-tabs__item ut2-scroll-item {if $selected_period == $key}active{/if}">
                                    <a href="{$curl|fn_link_attach:"abt__ut2_reviews_period={$key}&selected_section=product_reviews"|fn_url}"
                                       class="cm-ajax"
                                       data-ca-target-id="product_reviews_list_{$product_id}"
                                       rel="nofollow"
                                    >
                                        <span class="ty-tabs__span">{$period}</span>
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div>
            <div class="ty-product-reviews-view__main-content-right">
                {if $abt__ut2_product_reviews_gallery}
                    {include file="addons/product_reviews/views/product_reviews/components/product_reviews_gallery.tpl"}
                {/if}
                {include file="addons/product_reviews/views/product_reviews/components/product_reviews_most_helpful.tpl"}
                {include file="addons/product_reviews/views/product_reviews/components/product_reviews_navigation.tpl"
                total_product_reviews=$product.product_reviews_count
                }
                {if $product_reviews}
                    <div class="ty-product-review-view__posts">
                        {foreach $product_reviews as $product_review}
                            {include file="addons/product_reviews/views/product_reviews/components/post.tpl"
                            product_review=$product_review
                            }
                        {/foreach}
                    </div>
                {else}
                    <p class="ty-no-items">{__("product_reviews.no_reviews_found")}</p>
                {/if}

                {$reviews_left = $product_reviews_search.total_items - $product_reviews_search.page * $product_reviews_search.items_per_page}
                {if $product_reviews_search.total_items > $product_reviews_search.items_per_page && $reviews_left > 0}
                    <div class="ty-product-reviews-load-more ty-center">
                        <p class="ty-muted ty-center">
                            {$current_reviews_count = ($reviews_left>0)?$product_reviews_search.page * $product_reviews_search.items_per_page:$product_reviews_search.total_items}
                            {__('abt__ut2.product_reviews.showing_n_of_n_reviews', [ $product_reviews_search.total_items, '[count]' =>$current_reviews_count])}
                        </p>
                        {$data_load=$curl|fn_link_attach:"page=`$product_reviews_search.page+1`&sort_order=`$product_reviews_search.sort_order`&sort_by=`$product_reviews_search.sort_by`&only_buyers=`$product_reviews_search.only_buyers`&with_images=`$product_reviews_search.with_images`&abt__ut2_reviews_rating=`$product_reviews_search.abt__ut2_reviews_rating`"}
                        <button id="reviews-loader-btn" type="button" class="ty-btn ty-btn__outline" data-load-more="{$data_load|fn_url}">
                            {$load_more_reviews_count = ($reviews_left > $product_reviews_search.items_per_page) ? $product_reviews_search.items_per_page : $reviews_left}
                            {__('abt__ut2.product_reviews.show_n_more_reviews', [$load_more_reviews_count])}
                        </button>
                    </div>
                {/if}
            </div>
            <!--product_reviews_list_{$product_id}--></div>

        {if $product.product_reviews_count < 1 || $settings.ab__device === 'mobile'}
            {include file="addons/product_reviews/views/product_reviews/components/product_reviews_sidebar.tpl"
            product=$product
            product_id=$product_id
            locate_to_product_review_tab=$locate_to_product_review_tab
            product_reviews=$product_reviews
            }
        {/if}
    </section>

    {if $wrap == true}
    {/capture}
        {$smarty.capture.content nofilter}
    {else}
        {capture name="mainbox_title"}{$title}{/capture}
    {/if}
    <!--content_product_reviews--></div>

{script src="js/addons/product_reviews/index.js"}
{script src="js/addons/abt__unitheme2/abt__ut2_product_reviews.js"}
