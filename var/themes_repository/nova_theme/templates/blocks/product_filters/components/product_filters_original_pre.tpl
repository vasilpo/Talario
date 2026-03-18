{* Top panel with Filters and Reset buttons on mobile *}
{strip}
<div class="ty-product-filters__top-panel" id="product_filters_top_panel_{$block.block_id}">
    {* Get selected variants count *}
    {$selected_variants_count = 0}
    {if $items}
        {foreach $items as $filter_item}
            {if $filter_item.selected_variants && $filter_item.selected_variants|sizeof > 0}
                {$selected_variants_count = $selected_variants_count + $filter_item.selected_variants|sizeof}
            {elseif $filter_item.selected_range}
                {$selected_variants_count = $selected_variants_count + 1}
            {/if}
        {/foreach}
    {/if}

    {* Filters button on top panel *}
    <button id="opener_product_filters_original_{$block.block_id}" {""}
        type="button" {""}
        class="cm-dialog-opener cm-dialog-no-scroll ty-btn ty-product-filters__top-panel-link" {""}
        data-ca-dialog-title="{$block.name|default:__("storefront_filters")}" {""}
        data-ca-target-id="content_product_filters_original_{$block.block_id}" {""}
    >
        <span class="ty-product-filters__top-panel-link-badge">
            {if $selected_variants_count > 0}
                {$selected_variants_count}
            {/if}
        </span>
        {include_ext file="common/icon.tpl" class="ty-icon-filter"}
        <span class="ty-product-filters__top-panel-link-text">{$block.name|default:__("storefront_filters")}</span>
    </button>

    {* Reset button on top panel *}
    {if $ajax_div_ids}
        <a href="{$filter_base_url|fn_url}" {""}
            rel="nofollow" {""}
            class="ty-btn ty-product-filters__top-panel-reset cm-ajax cm-ajax-full-render cm-history {""}
                {if !$is_selected_filters}ty-product-filters__top-panel-reset--hide{/if} {""}
                {if !$products}ty-btn__primary{/if}" {""}
            data-ca-event="ce.filtersinit" {""}
            data-ca-target-id="{$ajax_div_ids}" {""}
        >
            {include_ext file="common/icon.tpl"
                class="ty-icon-cw"
            }
            <span class="ty-product-filters__top-panel-reset-text">{__("reset")}</span>
        </a>
    {/if}
<!--product_filters_top_panel_{$block.block_id}--></div>
{/strip}

{* Filter popup container for mobile *}
<div class="ty-product-filters__outer" data-ca-product-filters="popup" id="content_product_filters_original_{$block.block_id}">