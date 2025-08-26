{if $promotion.ab__dotd_awaited}
    {$to_timestamp = $promotion.from_date}
{elseif $addons.ab__deal_of_the_day.count_to == 'end_of_the_day' || !$promotion.to_date}
    {$to_timestamp = ('tomorrow midnight'|strtotime)}
{else}
    {$to_timestamp = $promotion.to_date}
{/if}

{$total_seconds = $to_timestamp - $smarty.const.TIME}

{if $total_seconds}
    <div id="ab__deal_of_the_day_{$block.block_id}" class="{if $addons.ab__deal_of_the_day.countdown_type === "flipclock"}flipclock{else}js-counter{/if}"></div>

    {if $addons.ab__deal_of_the_day.countdown_type === "flipclock"}
        {include file="addons/ab__deal_of_the_day/components/counters/flipclock.tpl"}
    {else}
        {include file="addons/ab__deal_of_the_day/components/counters/javascript.tpl"}
    {/if}
{/if}