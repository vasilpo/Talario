<div class="ab__dotd_promotion {if $promotion.ab__dotd_expired}action-is-over{/if}">
    <div class="row-fluid ab__dotd_promotion-main_info">
        {if $promotion.image}
            <div class="span8 ab__dotd_promotion-image">
                {include file="common/image.tpl" images=$promotion.image}
            </div>
        {/if}
        <div class="span8 ab__dotd_promotion-content">
            {hook name="ab__deal_of_the_day:promotion_page_header"}
                <h1>{$promotion.h1|default:$promotion.name nofilter}
                    {if $promotion.ab__dotd_expired}
                        <span>({__('ab__dotd.promotion_expired')})</span>
                    {elseif $promotion.ab__dotd_awaited}
                        <span>({__('ab__dotd.promotion_awaited')})</span>
                    {/if}
                </h1>
            {/hook}

            <div class="ab__dotd_promotion-description ty-wysiwyg-content">{$promotion.detailed_description nofilter}</div>
            {if $promotion.show_counter_on_promotion_page === "Y" && (($promotion.ab__dotd_active && $promotion.to_date) || $promotion.ab__dotd_awaited)}
                <div class="ab__dotd_promotion-timer">
                    <div class="ab__dotd_promotion-timer_title"><b>{if $promotion.ab__dotd_awaited}{__('ab__dotd_time_awaited_left')}{else}{__('ab__dotd_time_left')}{/if}:</b></div>
                    {include file="addons/ab__deal_of_the_day/components/init_countdown.tpl"}
                </div>
            {/if}
            {if $promotion.to_date || $promotion.from_date}
                <div class="ab__dotd_promotion_date">
                    <p>{__("ab__dotd.page_action_period")}
                        {if $promotion.from_date}
                            {__('ab__dotd.from')} {$promotion.from_date|date_format:"`$settings.Appearance.date_format`"}
                        {/if}
                        {if $promotion.to_date}
                            {__('ab__dotd.to')} {$promotion.to_date|date_format:"`$settings.Appearance.date_format`"}
                        {/if}
                    </p>
                </div>
            {/if}
            {if !$promotion.ab__dotd_active}
                <div class="actions-link"><a href="{"promotions.list"|fn_url}">{__("active_promotions")}</a></div>
            {/if}
        </div>
    </div>

    {if $promotion.hide_products_block != 'Y'}
        {if $promotion.use_products_filter == 'Y' && ($products || $smarty.request.features_hash)}
            <div id="selected_filters_ypi">{$smarty.capture.abt__selected_filters nofilter}<!--selected_filters_ypi--></div>
        {/if}


        {if $promotion.use_products_filter == 'Y' && ($products || $smarty.request.features_hash)}
        <div class="ypi-filters-container">
            <a class="ypi-white-vfbt"><i class="material-icons">&#xE16D;</i></a>
            <span class="f-title hidden">{__("filters")}</span>
        </div>
        {/if}

        <div class="cat-view-grid" id="category_products_{$block.block_id}">
            <div class="ab__dotd_promotions-products" id="promotion_products">
            {if $products}
                {assign var="layouts" value=""|fn_get_products_views:false:0}
                {if $layouts.$selected_layout.template}
                    {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_products_list}
                {/if}
            {/if}
            <!--promotion_products--></div>
        <!--category_products_{$block.block_id}--></div>
        {if $category_groups}
            {assign var="layouts" value=""|fn_get_products_views:false:0}
            {$tmpl_extra="products_multicolumns_extra"}
            {foreach $category_groups as $category_group}
                <div class="clearfix ty-mb-s">
                    <h2 class="ty-mainbox-title">{$category_group.category}</h2>
                    {$products = $category_group.products}
                    {if $layouts.$selected_layout.template}
                        {include file="`$layouts.$selected_layout.template`" columns=$settings.Appearance.columns_in_products_list}
                    {/if}
                </div>
            {/foreach}
        {/if}
    {/if}
</div>