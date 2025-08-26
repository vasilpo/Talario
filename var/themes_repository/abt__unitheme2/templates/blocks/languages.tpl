{if $languages && $languages|count > 1}

<div class="ut2-languages clearfix ab__ut2--languages {if $dropdown_limit >= $languages|count}ab__ut2--languages_mode_plain{else}ab__ut2--languages_mode_dropdown{/if}" id="languages_{$block.block_id}">

    {$uid = uniqid()}

    {if $text}<div class="ty-select-block__txt">{$text}</div>{/if}

    {* render dropdown *}
    {if $format == "ab__name_without_icons"}
        {assign var="key_name" value="name"}
        {assign var="icon_true" value=false}
    {elseif $format == "name"}
        {assign var="key_name" value="name"}
        {assign var="icon_true" value=true}
    {else}
        {assign var="key_name" value=""}
        {assign var="icon_true" value=true}
    {/if}
    <div class="ty-select-wrapper ab__ut2--languages__dropdown{if $format == "icon"} format-only-icon ty-languages__icon-link{/if}">{include file="common/select_object.tpl" style="graphic" suffix="language_{$uid}" link_tpl=$config.current_url|fn_link_attach:"sl=" items=$languages selected_id=$smarty.const.CART_LANGUAGE display_icons=$icon_true key_name=$key_name language_var_name="sl" link_class="" text=false}</div>{* END .ab__ut2--languages__dropdown *}


    {* render plain list *}
    {if $dropdown_limit >= $languages|count}

        <div class="ty-select-wrapper ty-languages clearfix ab__ut2--languages__plain-list{if $format == "icon"} format-only-icon{/if}">
            {foreach from=$languages key=code item=language}
                <a href="{$config.current_url|fn_link_attach:"sl=`$language.lang_code`"|fn_url}" title="{__("change_language")}" class="ty-languages__item{if $format === "icon"} ty-languages__icon-link{/if}{if $smarty.const.DESCR_SL === $code} ty-languages__active{/if}">
                    {if $format != "ab__name_without_icons" || $format === "icon"}
                        {include file="common/icon.tpl" class="ty-flag ty-flag-`$language.country_code|lower`" code="`$language.country_code`" format="`$format`"}
                        {if $format != 'icon'}<span>{$code|upper}</span>{/if}
                    {elseif $format === "ab__name_without_icons"}
                        <span style="text-transform: uppercase">{$language.lang_code}</span>
                    {else}
                        {$language.name}
                    {/if}
                </a>
            {/foreach}
        </div>{* END .ab__ut2--languages__plain-list *}

    {/if}

<!--languages_{$block.block_id}--></div>{* END .ab__ut2--languages *}
{/if}
