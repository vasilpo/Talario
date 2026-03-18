{if fn_upsell_is_upsellable($feature)}
    {$popup_id="upsell_help_block_popup_{$feature}"}
    {$is_control_group=$is_control_group|default:true}

    <div class="{if $is_control_group}control-group{/if}">
        <div class="{if $is_control_group}controls{/if}">
            <div class="well well-small help-block">
                {$text} <a data-ca-target-id="{$popup_id}" class="cm-dialog-opener cm-dialog-auto-height">{__("upsell.learn_more")}</a>
            </div>
        </div>
    </div>

    {include file="addons/upsell/components/popup.tpl" popup_id=$popup_id feature=$feature popup_content_pre=$popup_content_pre}
{/if}
