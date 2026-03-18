{strip}
{* Set $navbar_mobile_items data *}
{include file="components/navbar_mobile/navbar_mobile_data.tpl"}

{* Process $navbar_mobile_items data *}
{include file="components/navbar_mobile/navbar_mobile_process.tpl"}

{$item_text_characters_threshold = 10}

<div data-ca-navbar-mobile="main" class="ty-navbar-mobile" id="navbar_mobile">
    <div data-ca-navbar-mobile="inner" class="ty-navbar-mobile__inner">
        {foreach $navbar_mobile_items as $item}
            {$is_active_item = ($item.id === "homepage" && !$runtime.controller|in_array:$item.not_controllers)
                || $runtime.controller|in_array:$item.controllers
            }
            {$item_text = $item.text|default:($item.id|capitalize:true)}
            <div data-ca-navbar-mobile="item" class="ty-navbar-mobile__item" {if $item.container_id}id="{$item.container_id}"{/if}>
                <a {if $item.href || $item.href === ""}href="{$item.href|fn_url}"{/if} {""}
                    rel="nofollow" {""}
                    class="ty-navbar-mobile__item-link ty-navbar-mobile__item-link--{$item.id} {""}
                        {if $is_active_item}ty-navbar-mobile__item-link--active{/if} {""}"
                >
                    <span class="ty-navbar-mobile__item-icon-wrapper">
                        <span class="ty-navbar-mobile__item-badge">{if $item.badge}{$item.badge}{/if}</span>
                        {if $item.custom_icon}
                            <span class="ty-navbar-mobile__item-custom-icon-wrapper {""}
                                {if $is_active_item}ty-navbar-mobile__item-custom-icon-wrapper--active {""}{/if}">
                                {$item.custom_icon nofilter}
                            </span>
                        {else}
                            {include_ext file="common/icon.tpl" class="ty-navbar-mobile__item-icon `$item.icon|default:'ty-icon-file'`"}
                        {/if}
                    </span>
                    <span class="ty-navbar-mobile__item-text {if $item_text|count_characters:true >= $item_text_characters_threshold}ty-navbar-mobile__item-text--long{/if}">
                        {$item_text}
                    </span>
                </a>
            {if $item.container_id}<!--{$item.container_id}-->{/if}</div>
        {/foreach}
    </div>
<!--navbar_mobile--></div>
{/strip}