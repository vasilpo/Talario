<div class="ut2-items level-{$level}">
{foreach from=$categories item=$category name=categories}
    {if $level == 2 && $smarty.foreach.categories.iteration == $block.properties.abt__ut2_category_count_level_2+1}
        <div class="ut2-item ut2-more-btn" data-action="show"><span>+{$smarty.foreach.categories.total - $block.properties.abt__ut2_category_count_level_2}<span>{__("more")}</span><i class="ty-icon-down-open"></i></span></div>
        {$hidden_item = 'hidden-item hidden'}
    {elseif $level == 3 && $smarty.foreach.categories.iteration == $block.properties.abt__ut2_category_count_level_3+1}
        <div class="ut2-item ut2-more-btn" data-action="show"><span>+{$smarty.foreach.categories.total - $block.properties.abt__ut2_category_count_level_3}<span>{__("more")}</span><i class="ty-icon-down-open"></i></span></div>
        {$hidden_item = 'hidden-item hidden'}
    {/if}
    {if $category.current}
        {$parent = false}
    {/if}

    <div class="{if $category.current} current{/if}{if $parent} parent{else} ut2-item{/if} {$hidden_item}">
        {if $category.current}
            <span>{$category.category nofilter}
                {if $block.properties.abt__ut2_show_products_count == "YesNo::YES"|enum}
                    <em>{{fn_abt__ut2_get_category_products_count($category.category_id,true)}}</em>
                {/if}
            </span>
        {else}
            {if $parent}<div class="ut2-item">{/if}
            <a href="{"categories.view?category_id=`$category.category_id`"|fn_url}">
                {if $parent}
                    <span><i class="ut2-icon-arrow_back_black"></i>{$category.category nofilter}</span>
                {else}
                    {$category.category nofilter}
                {/if}

                {if $block.properties.abt__ut2_show_products_count == "YesNo::YES"|enum}
                    <em>{{fn_abt__ut2_get_category_products_count($category.category_id,true)}}</em>
                {/if}
            </a>
            {if $parent}</div>{/if}
        {/if}

        {if $category.subcategories}
            {include file="addons/abt__unitheme2/blocks/components/abt__ut2_advanced_subcategories_menu_level.tpl"
            categories=$category.subcategories
            level=$level+1
            parent=$parent
            }
        {/if}
    </div>
    {if $level == 2 && $smarty.foreach.categories.iteration > $block.properties.abt__ut2_category_count_level_2 && $smarty.foreach.categories.iteration == $smarty.foreach.categories.total}
        <div class="ut2-item ut2-more-btn {$hidden_item}" data-action="hide"><span><span>{__("less")}</span><i class="ty-icon-up-open"></i></span></div>
        {$hidden_item = 'hidden-item hidden'}
    {elseif $level == 3 && $smarty.foreach.categories.iteration > $block.properties.abt__ut2_category_count_level_3  && $smarty.foreach.categories.iteration == $smarty.foreach.categories.total}
        <div class="ut2-item ut2-more-btn {$hidden_item}" data-action="hide"><span><span>{__("less")}</span><i class="ty-icon-up-open"></i></span></div>
        {$hidden_item = 'hidden-item hidden'}
    {/if}
{/foreach}
</div>

