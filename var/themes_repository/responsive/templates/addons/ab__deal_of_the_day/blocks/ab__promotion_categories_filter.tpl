{** block-description:ab__promotion_categories_filter **}
{if $categories}
<div class="ab-dotd-categories-filter">
    <ul>
        {foreach $categories as $category}
            {include file="addons/ab__deal_of_the_day/components/category_filter_item.tpl" category=$category}
        {/foreach}
    </ul>
</div>
{/if}
