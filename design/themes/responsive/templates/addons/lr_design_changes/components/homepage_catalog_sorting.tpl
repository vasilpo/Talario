{$pagination_id = $target_id|default:"pagination_contents"}
{$curl = $base_url|fn_query_remove:"sort_by":"sort_order":"result_ids":"layout":"page":"items_per_page"}
{$sorting = ""|fn_get_products_sorting}
{$sorting_orders = ""|fn_get_products_sorting_orders}
{$avail_sorting = $settings.Appearance.available_product_list_sortings}

{if !$config.tweaks.disable_dhtml}
    {$ajax_class = "cm-ajax"}
{/if}

<div class="ty-sort-container">
    {$current_sort_by = $search.sort_by|default:"product"}
    {$current_sort_order = $search.sort_order|default:"asc"}
    {$current_sort_order_rev = $search.sort_order_rev|default:"desc"}
    {if $avail_sorting}
        <div class="ut2-sorting-wrap">
            {if strpos($curl, '?') === false}
                {$curl = $curl|fn_link_attach:"dispatch=index.index"}
            {/if}
            {include file="common/sorting.tpl"
                sorting=$sorting
                sorting_orders=$sorting_orders
                search=$search
                avail_sorting=$avail_sorting
                pagination_id=$pagination_id
            }

            {$pagination = $search|fn_generate_pagination}
            {if $pagination.total_items}
                {$range_url = $base_url|fn_query_remove:"items_per_page":"page"}
                {if strpos($range_url, '?') === false}
                    {$range_url = $range_url|fn_link_attach:"dispatch=index.index"}
                {/if}
                {if $request.features_hash}
                    {$range_url = $range_url|fn_link_attach:"features_hash=`$request.features_hash`"}
                {/if}
                {if $current_sort_by}
                    {$range_url = $range_url|fn_link_attach:"sort_by=`$current_sort_by`"}
                {/if}
                {if $current_sort_order}
                    {$range_url = $range_url|fn_link_attach:"sort_order=`$current_sort_order`"}
                {/if}

                {$product_steps = $settings.Appearance.columns_in_products_list|fn_get_product_pagination_steps:$settings.Appearance.products_per_page}
                <div class="ty-sort-dropdown">
                    <div class="ut2-sort-label">{__("show")}:</div>
                    <a id="sw_elm_pagination_steps_lrhc" class="ty-sort-dropdown__wrapper cm-combination"><span>{$pagination.items_per_page}</span><i class="ut2-icon-outline-expand_more"></i></a>
                    <div id="elm_pagination_steps_lrhc" class="ty-sort-dropdown__content cm-popup-box hidden">
                        <span class="ut2-popup-box-title">{$pagination.items_per_page} {__("per_page")}<span class="cm-external-click ut2-btn-close" data-ca-external-click-id="sw_elm_pagination_steps_lrhc"><i class="ut2-icon-baseline-close"></i></span></span>
                        <ul>
                            {foreach from=$product_steps item="step"}
                                {if $step != $pagination.items_per_page}
                                    {$step_url = $range_url|fn_link_attach:"items_per_page=`$step`"}
                                    <li class="ty-sort-dropdown__content-item">
                                        <a class="{$ajax_class} cm-ajax-full-render ty-sort-dropdown__content-item-a" href="{$step_url|fn_url}" data-ca-target-id="{$pagination_id}" rel="nofollow">{$step} {__("per_page")}</a>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>
    {/if}
</div>
