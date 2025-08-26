{$id="simple_products_scroller_{$ab__mb_id|default:"`$block.block_id`_`$product.product_id`"}"}
{$elements_to_scroll = 1}

{strip}
<div class="ab-mb-horizontal-tabs-wrap">
    <div class="ty-tabs ut2-scroll-container" id="{$id}">
        <button class="ut2-scroll-left" type="button"><span class="ty-icon ty-icon-left-open-thin"></span></button>
        <div class="ab-mb-horizontal-tabs ty-tabs__list ut2-scroll-content">
            {foreach $ab__motivation_items as $ab__mb_item}
                {$key = "`$id`_`$ab__mb_item.motivation_item_id`"}
                {$capture_name = "mb__content_`$key`"}

                {capture name=$capture_name}
                    {hook name="ab__mb:horizontal_tab_content"}
                        <div class="ab__mb_item-description">
                            {hook name="ab__mb:templates_content"}
                                {if $ab__mb_item.template_path == 'addons/ab__motivation_block/blocks/components/item_templates/geo_maps.tpl'}
                                    {$product_id = $product.product_id}
                                {/if}
                            {/hook}

                            {$is_vendor_description = false}
                            {if $ab__mb_item.is_vendor_description}
                                {$is_vendor_description = true}
                            {/if}

                            {include file=$ab__mb_item.template_path is_vendor_description=$is_vendor_description}
                        </div>
                    {/hook}
                {/capture}

                {if trim(strip_tags($smarty.capture.$capture_name, '<img>'))}
                    {$is_icon = $ab__mb_item.icon_type == 'icon' && $ab__mb_item.icon_class}
                    {$is_image = $ab__mb_item.icon_type == 'img' && $ab__mb_item.main_pair}

                    {$is_anything = $is_icon || $is_image}
                    <div class="ab-mb-horizontal__item-tab ty-tabs__item ut2-scroll-item {if $addons.ab__motivation_block.save_element_state == {"YesNo::YES"|enum}} ab-mb-save-state{/if}{if $smarty.cookies.$key == {"YesNo::YES"|enum}} active{/if}{if $is_anything} has-icon{/if}" data-mb-id="{$key}">
                        {hook name="ab__mb:horizontal_tab_title"}
                            <div class="ab-mb-horizontal__title-tab ty-tabs__span">
                                <span {live_edit name="ab__motivation_block:name:`{$ab__mb_item.motivation_item_id}`"}>
                                    {if $is_icon}
                                        <i class="{$ab__mb_item.icon_class}" style="color: {$ab__mb_item.icon_color}"></i>
                                    {elseif $is_image}
                                        {include file="common/image.tpl" images=$ab__mb_item.main_pair}
                                    {/if}
                                    {$ab__mb_item.name}
                                </span>
                            </div>
                        {/hook}
                    </div>
                {/if}
            {/foreach}
        </div>
        <button class="ut2-scroll-right" type="button"><span class="ty-icon ty-icon-right-open-thin"></span></button>
    </div>
    <div class="ab-mb-horizontal-content">
        {foreach $ab__motivation_items as $ab__mb_item}
        	{$key = "`$id`_`$ab__mb_item.motivation_item_id`"}
            {$capture_name = "mb__content_`$key`"}
            {if $smarty.capture.$capture_name|trim}
                <div class="ab-mb-horizontal__item{if $smarty.cookies.$key == {"YesNo::YES"|enum}} active{/if}" data-mb-id="{$key}">
                    {$smarty.capture.$capture_name nofilter}
                </div>
            {/if}
        {/foreach}
    </div>
</div>
{/strip}

{include file="common/simple_scroller_init.tpl" block_id=$id elements_to_scroll=$elements_to_scroll}