{if $currencies && $currencies|count > 1}

    <div id="currencies_{$block.block_id}" class="ut2-currencies clearfix ab__ut2--currencies {if $dropdown_limit >= $currencies|count}ab__ut2--currencies_mode_plain{else}ab__ut2--currencies_mode_dropdown{/if}">

        {$uid = uniqid()}

        {if $text}<div class="ty-select-block__txt">{$text}</div>{/if}

        {* render dropdown *}
        {if $format == "name"}
            {assign var="key_name" value="description"}
        {else}
            {assign var="key_name" value=""}
        {/if}
        <div class="ty-select-wrapper ab__ut2--currencies__dropdown{if $format != "name"} format-only-symbol{/if}">{include file="common/select_object.tpl" style="graphic" suffix="currency_{$uid}" link_tpl=$config.current_url|fn_link_attach:"currency=" items=$currencies selected_id=$secondary_currency display_icons=false key_name=$key_name text=false}</div>{* END .ab__ut2--currencies__dropdown *}


        {* render plain list *}
        {if $dropdown_limit >= $currencies|count}

            <div class="ty-select-wrapper ty-currencies clearfix ab__ut2--currencies__plain-list{if $format != "name"} format-only-symbol{/if}">
                {foreach from=$currencies key=code item=currency}
                    <a href="{$config.current_url|fn_link_attach:"currency=`$code`"|fn_url}" rel="nofollow"
                       class="ty-currencies__item {if $secondary_currency == $code}ty-currencies__active{/if}">{if $format == "name"}{$currency.description}&nbsp;({$currency.symbol nofilter}){else}{$currency.symbol nofilter}{/if}</a>
                {/foreach}
            </div>{* END .ab__ut2--currencies__plain-list *}

        {/if}

        <!--currencies_{$block.block_id}--></div>{* END .ab__ut2--currencies *}
{/if}