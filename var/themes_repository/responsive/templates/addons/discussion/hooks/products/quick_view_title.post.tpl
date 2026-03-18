{if $product.discussion_type && $product.discussion_type != 'D' && $addons.product_reviews.status !== "ObjectStatuses::ACTIVE"|enum}
    {$show_write_product_review_button = $show_write_product_review_button|default:true}
    <div class="ty-discussion__rating-wrapper clearfix" id="average_rating_product_{$obj_prefix}{$obj_id}">
        {assign var="rating" value="rating_`$obj_id`"}{$smarty.capture.$rating nofilter}

        {if $product.discussion.posts}
        <a  href="{"products.view?product_id=`$product.product_id`&selected_section=discussion#discussion"|fn_url}" class="ty-discussion__review-a">{$product.discussion.search.total_items} {__("reviews", [$product.discussion.search.total_items])}</a>
        {/if}
        {if $show_write_product_review_button}
            {include
                file="addons/discussion/views/discussion/components/new_post_button.tpl"
                name=__("write_review")
                obj_id=$obj_id
                obj_prefix="quick_view_"
                style="text"
                object_type="Addons\\Discussion\\DiscussionObjectTypes::PRODUCT"|enum
            }
        {/if}
    <!--average_rating_product_{$obj_prefix}{$obj_id}--></div>
{/if}
