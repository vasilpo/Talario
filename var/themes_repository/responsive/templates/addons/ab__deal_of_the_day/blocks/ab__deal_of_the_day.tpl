{** block-description:ab__deal_of_the_day **}

{if $promotion && $promotion.status == 'A' &&
(!$promotion.to_date || $promotion.to_date > $smarty.now) &&
(!$promotion.from_date || $promotion.from_date < $smarty.now)}

    {$products = $promotion|fn_ab__dotd_get_promotion_products:$block.properties}

    {if $products}
        <div class="ab__deal_of_the_day {if $block.properties.product_full_width == "Y"}pd-fullwidth{/if}">
            <div class="pd-block">
                <div class="pd-promotion__title">{$promotion.name}</div>

                <div class="promotion-descr">
	                {$promotion.short_description nofilter}
	                <a class="more" href="{"promotions.view?promotion_id=`$promotion.promotion_id`"|fn_url}" title="{__('ab__dotd.detailed')}">{__('ab__dotd.detailed')}</a>
                </div>

                {if $block.properties.ab__dotd_enable_countdown_timer == 'Y' || !$block.properties.ab__dotd_enable_countdown_timer}
                    <b class="time-left">{__('ab__dotd_time_left')}:</b>
                    {include file="addons/ab__deal_of_the_day/components/init_countdown.tpl"}
                {/if}

                <div class="all-promotions__button"><a href="{"promotions.list"|fn_url}" class="ty-btn ty-btn__secondary" title=""><span>{__("ab__dotd.all_promotions_list")}</span></a></div>
            </div>
            <div class="pd-content-block">
                {include file="addons/ab__deal_of_the_day/blocks/products/components/products_scroller_pd.tpl" items=$products}
            </div>
        </div>
    {/if}
{/if}