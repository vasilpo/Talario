{$is_active = $category.category_id == $selected_category_id}

{$url_pattern = "promotions.view?promotion_id=`$promotion.promotion_id`"}
{if $category.category_id}
    {$url_pattern = "`$url_pattern`&cid=`$category.category_id`"}
{/if}
{strip}
<li class="{if $category.level > 0}level-{$category.level} {/if}{if $category.all_categories}all-categories {/if}{if $category.icon}cat-icon {/if}{if $is_active}active{/if}">
    {if !$is_active}<a href="{$url_pattern|fn_url}">{/if}
        {if $category.icon}
        <span class="ab-dotd-filter-icon">{include file="common/image.tpl" images=$category.icon image_width=24}</span>
        {/if}
        {$category.category}{if $category.total_products} <span class="cat-count">({$category.total_products})</span>{/if}
    {if !$is_active}</a>{/if}
</li>
{if $category.subcategories}
    <li>
        <ul>
            {foreach $category.subcategories as $_category}
                {include file="addons/ab__deal_of_the_day/components/category_filter_item.tpl" category=$_category}
            {/foreach}
        </ul>
    </li>
{/if}
{/strip}