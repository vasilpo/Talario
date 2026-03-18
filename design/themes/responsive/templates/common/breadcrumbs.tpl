<div id="breadcrumbs_{$block.block_id}">

{if $breadcrumbs && $breadcrumbs|@sizeof > 1}{strip}
    {$breadcrumbs_slash_class = "ty-breadcrumbs__slash `$breadcrumbs_slash_class` "}
    {$breadcrumbs_a_class = "ty-breadcrumbs__a `$additional_class` `$breadcrumbs_a_class`"}
    {$breadcrumbs_current_class = "ty-breadcrumbs__current `$breadcrumbs_current_class`"}
    {/strip}
    <div class="ty-breadcrumbs clearfix">
        {strip}
            {include file="common/back_button.tpl"}
            {foreach from=$breadcrumbs item="bc" name="bcn" key="key"}
                {if $key != "0"}
                    <span class="{$breadcrumbs_slash_class}">/</span>
                {/if}
                {if $bc.link}
                    <a href="{$bc.link|fn_url}" class="{$breadcrumbs_a_class}"{if $bc.nofollow} rel="nofollow"{/if}>{$bc.title|strip_tags|escape:"html" nofilter}</a>
                {else}
                    <span class="{$breadcrumbs_current_class}"><bdi>{$bc.title|strip_tags|escape:"html" nofilter}</bdi></span>
                {/if}
            {/foreach}
            {include file="common/view_tools.tpl"}
        {/strip}
    </div>
{/if}
<!--breadcrumbs_{$block.block_id}--></div>
