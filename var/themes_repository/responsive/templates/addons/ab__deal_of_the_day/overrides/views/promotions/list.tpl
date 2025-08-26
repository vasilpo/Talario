{include file="common/pagination.tpl"}

{$image_height="200"}
{$image_width="330"}

<div class="ab__dotd_promotions clearfix">
    {hook name="promotions:promotion_list"}
    {if $promotions}
        {foreach $promotions as $promotion}
            {if $promotion.promotion_id}
                <div class="ab__dotd_promotions-item {if !$promotion.ab__dotd_active}ab__dotd_promotion_expired{/if}">
                    <div class="ab__dotd_promotions-item_image" style="max-height: {$image_height}px;">
                        <a href="{"promotions.view?promotion_id=`$promotion.promotion_id`"|fn_url}">
                            {include file="common/image.tpl" images=$promotion.image image_height=$image_height image_width=$image_width no_ids=true}
                        </a>
                    </div>

                    {if $promotion.ab__dotd_active && $promotion.to_date}
                        {assign var="days_left" value=(($promotion.to_date-$smarty.now)/86400)|ceil}
                        <div class="ab__dotd_promotions-item_days_left{if $days_left <= $addons.ab__deal_of_the_day.highlight_when_left} ab__dotd_highlight{/if}">
                            {if $days_left > 1}
                                {__('ab__dotd.days_left', [$days_left])}
                            {else}
                                {__('ab__dotd.today_only')}
                            {/if}
                        </div>
                    {elseif $promotion.ab__dotd_awaited}
                        {assign var="days_left" value=(1-($smarty.now-$promotion.from_date)/86400)|floor}
                        <div class="ab__dotd_promotions-item_days_left">
                            {__('ab__dotd.days_to_start', [$days_left])}
                        </div>
                    {elseif $promotion.ab__dotd_expired}
                        <div class="ab__dotd_promotions-item_days_left">
                            {__('ab__dotd.promotion_expired')}
                        </div>
                    {/if}

                    <div class="ab__dotd_promotions-item_title">
                        <a href="{"promotions.view?promotion_id=`$promotion.promotion_id`"|fn_url}" title="">{$promotion.name nofilter}</a>
                        <div class="ab__dotd_promotions-item_date">
                            {if $promotion.from_date}
                                {__('ab__dotd.from')} {$promotion.from_date|date_format:"`$settings.Appearance.date_format`"}
                            {/if}
                            {if $promotion.to_date}
                                {__('ab__dotd.to')} {$promotion.to_date|date_format:"`$settings.Appearance.date_format`"}
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}
        {/foreach}
    {else}
        <p>{__("text_no_active_promotions")}</p>
    {/if}
    {/hook}
</div>
{include file="common/pagination.tpl"}

{if $show_chains && $chains}
<div class="ab__dotd_chains">
    <div class="ab__dotd_chains_title">{__('ab__dotd.chains_list.title')}</div>
    <div class="ab__dotd_chains_content">
        {include file="addons/buy_together/blocks/product_tabs/buy_together.tpl" chains=$chains}
        {if $chains_search.total_pages > 1}
            <button class="ty-btn ty-btn__tertiary ab__dotd_chains-show_more">
                {__('ab__dotd.get_more_combinations', [$chains_search.more])}
                <em>{__('ab__dotd.showed_combinations', ['[n]' => $chains_search.items_per_page, '[total]' => $chains_search.total_items])}</em>
            </button>
        {/if}
    </div>
</div>
{/if}

{capture name="mainbox_title"}{__("promotions")}{/capture}