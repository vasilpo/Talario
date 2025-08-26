{if $promotion && $category_group}

{*Remove empty cells*}
{capture name="iteration"}0{/capture}

<div class="ty-column{$columns} ab-dotd-more-products">
    <div class="ut2-gl__item">
        <div class="ut2-gl__body" {if $smarty.capture.abt__ut2_gl_item_height}style="position: initial;height: {$smarty.capture.abt__ut2_gl_item_height nofilter}px;width: auto;padding: 0;"{/if}>
            <a href="{"promotions.view?promotion_id=`$promotion.promotion_id`&cid=`$category_group.category_id`"|fn_url}">
                <span class="ty-icon ty-icon-arrow-up-right ab-dotd-more-icon"></span>
                {__('ab__dotd.more_products_from_category', ["[category]" => $category_group.category])}
            </a>
        </div>
    </div>
</div>
{/if}