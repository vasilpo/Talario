{$language_text = $text|default:__("select_descr_lang")}
{$show_group = $show_group|default:false}
{$show_button_text = $show_button_text|default:true}
{$show_button_symbol = $show_button_symbol|default:true}
{if $style == "graphic"}{strip}

{/strip}{if $text}<div class="ty-select-block__txt hidden-phone hidden-tablet">{$text}:</div>{/if}{strip}
{/strip}{if $show_group}<div class="ty-btn-group {$class}">{/if}{strip}

{/strip}
    <a class="ty-select-block__a ab__ut2--select-block__head cm-combination {$button_class}" data-ca-toggle="dropdown" id="sw_select_{$selected_id}_wrap_{$suffix}">
        {*span wrapper needed for structure uniformity*}
        <span>
            {if $display_icons == true}
                {include_ext file="common/icon.tpl"
                class="ty-flag ty-select-block__a-flag ty-flag-`$items.$selected_id.country_code|lower`"
                }
            {/if}

            <span class="ty-select-block__a-item ab__ut2--select-block__head__text {if $link_class}{$link_class}{/if}">
                {$ab__symbol_head = $items.$selected_id.symbol}
                {$ab__code_head = $items.$selected_id.lang_code|upper}

                {if $show_button_text && $items.$selected_id.$key_name}

                    {if $ab__symbol_head}
                        {$ab__symbol_head = "<ins>(</ins>`$ab__symbol_head`<ins>)</ins>"}
                    {/if}

                    {if $ab__code_head}
                        {$ab__code_head = "<ins>(</ins>`$ab__code_head`<ins>)</ins>"}
                    {/if}
                    <span class="ab__ut2--select-block__head__text__name">
                        {$items.$selected_id.$key_name}
                    </span>
                {/if}

                {if $show_button_symbol && $ab__symbol_head}
                    <span class="ab__ut2--select-block__head__text__symbol">
                        {$ab__symbol_head nofilter}
                    </span>
                {/if}

                {if $ab__code_head}
                    <span class="ab__ut2--select-block__head__text__code">
                        {$ab__code_head nofilter}
                    </span>
                {/if}
            </span>

            {*{include_ext file="common/icon.tpl" class="ty-icon-down-micro ty-select-block__arrow"}*}
        </span>
    </a>

    <div id="select_{$selected_id}_wrap_{$suffix}" class="ty-select-block ab__ut2--select-block__popup cm-popup-box hidden">
        <ul class="cm-select-list ty-select-block__list ty-flags">
            {foreach from=$items item=item key=id}

                <li class="ty-select-block__list-item ab__ut2--select-block__popup__item">
                    <a rel="nofollow" href="{"`$link_tpl``$id`"|fn_url}" class="ty-select-block__list-a {if $selected_id == $id}is-active{/if} {if $suffix == "live_editor_box"}cm-lang-link{/if} {$dropdown_menu_item_link_class}" {if $display_icons == true}data-ca-country-code="{$item.country_code|lower}"{/if} data-ca-name="{$id}"
                            {if $item.symbol}
                                data-ca-list-item-symbol="{$item.symbol}"
                            {/if}
                    >
                        {if $display_icons == true}
                            {include_ext file="common/icon.tpl"
                            class="ty-flag ty-flag-`$item.country_code|lower`"
                            }
                        {/if}
                        
                        <span class="ab__ut2--select-block__popup__text">
                            {$ab__symbol_popup = $item.symbol}
                            {$ab__code_popup = $item.lang_code|upper}

                            {if $item.$key_name}

                                {if $ab__symbol_popup}
                                    {$ab__symbol_popup = "<ins>(</ins>`$ab__symbol_popup`<ins>)</ins>"}
                                {/if}

                                {if $ab__code_popup}
                                    {$ab__code_popup = "<ins>(</ins>`$ab__code_popup`<ins>)</ins>"}
                                {/if}

                                <span class="ab__ut2--select-block__popup__text__name">
                                    {$item.$key_name nofilter}
                                </span>
                            {/if}

                            {if $ab__symbol_popup}
                                <span class="ab__ut2--select-block__popup__text__symbol">
                                    {$ab__symbol_popup nofilter}
                                </span>
                            {/if}

                            {if $ab__code_popup}
                                <span class="ab__ut2--select-block__popup__text__code">
                                    {$ab__code_popup nofilter}
                                </span>
                            {/if}
                        </span>
                        
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
    {if $show_group}</div>{/if}
{else}
    {if $text}<label for="id_{$var_name}" class="ty-select-block__txt hidden-phone hidden-tablet">{$text}:</label>{/if}
    <select id="id_{$var_name}" name="{$var_name}" onchange="Tygh.$.redirect(this.value);" class="ty-valign">
        {foreach from=$items item=item key=id}
            <option value="{"`$link_tpl``$id`"|fn_url}" {if $id == $selected_id}selected="selected"{/if}>{$item.$key_name nofilter}</option>
        {/foreach}
    </select>
{/if}
