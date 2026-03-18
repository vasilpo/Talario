{if $content|trim}
    {assign var="dropdown_id" value=$block.snapping_id}
    <div class="ty-dropdown-box {if $block.user_class} {$block.user_class}{/if}{if $content_alignment === "RIGHT"} ty-float-right{elseif $content_alignment === "LEFT"} ty-float-left{/if}{if isset($block.properties.abt__menu_compact_view) && $block.properties.abt__menu_compact_view === "YesNo::YES"|enum} compact{/if}">

        <div id="sw_dropdown_{$dropdown_id}" class="ty-dropdown-box__title cm-combination {if $header_class} {$header_class}{/if}{if $settings.ab__device !== "desktop" && !($block.type === "menu" && $block.properties.template|strpos:"dropdown_" !== false)}cm-abt--ut2-toggle-scroll{/if}">
            {hook name="wrapper:onclick_dropdown_title"}
            {if $smarty.capture.title && $smarty.capture.title|trim}
                {$smarty.capture.title nofilter}
            {else}
                <span><i class="ut2-icon"></i><span>{$title nofilter}</span></span>
            {/if}
            {/hook}
        </div>

        <div id="dropdown_{$dropdown_id}" class="cm-popup-box ty-dropdown-box__content {if $content_class} {$content_class}{/if} hidden">
            <div class="ty-dropdown-box__title cm-external-click {if $header_class} {$header_class}{/if} hidden-desktop" data-ca-external-click-id="sw_dropdown_{$dropdown_id}">
                <i class="ut2-icon"></i>{$title nofilter}<span class="ut2-btn-close"><i class="ut2-icon-baseline-close"></i></span>
            </div>
            {$content|default:"&nbsp;" nofilter}
        </div>

        <div class="cm-external-click ui-widget-overlay hidden" data-ca-external-click-id="sw_dropdown_{$dropdown_id}"></div>
    </div>
{/if}
