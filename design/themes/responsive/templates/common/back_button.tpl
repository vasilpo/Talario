{if $enable_back_button}{strip}
    {$breadcrumbs_back_button_class = "ty-btn ty-btn-icon breadcrumbs__back-button `$breadcrumbs_back_button_class`"}

    {if $enable_back_button_responsive}
        {$breadcrumbs_slash_class = "`$breadcrumbs_slash_class` ty-breadcrumbs__slash--hide-mobile"}
        {$breadcrumbs_a_class = "`$breadcrumbs_a_class` ty-breadcrumbs__a--hide-mobile"}
        {$breadcrumbs_current_class = "`$breadcrumbs_current_class` ty-breadcrumbs__current--hide-mobile"}
        {$breadcrumbs_back_button_class = "`$breadcrumbs_back_button_class` breadcrumbs__back-button--hide-desktop"}
    {/if}

    {$last_node_with_link = []}
    {foreach $breadcrumbs as $item}
        {if !empty($item.link)}
            {$last_node_with_link = $item}
        {/if}
    {/foreach}
    {if !$last_node_with_link}
        {$last_node_with_link = [
            title => __("home"),
            link => ""
        ]}
    {/if}

    <a href="{$last_node_with_link.link|fn_url}" class="{$breadcrumbs_back_button_class}"{if $last_node_with_link.nofollow} rel="nofollow"{/if} title="{$last_node_with_link.title|strip_tags|escape:"html" nofilter}">
        {include_ext file="common/icon.tpl" class="ty-icon-left-open"}
    </a>

    {if $enable_back_button_responsive}
        {$breadcrumbs_slash_class = $breadcrumbs_slash_class scope=parent}
        {$breadcrumbs_a_class = $breadcrumbs_a_class scope=parent}
        {$breadcrumbs_current_class = $breadcrumbs_current_class scope=parent}
    {/if}
{/strip}{/if}