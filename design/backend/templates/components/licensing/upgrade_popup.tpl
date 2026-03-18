{if $upgrade_feature}
    {$auto_open=$auto_open|default:true}
    {$popup_id=$popup_id|default:"licensing_upgrade_popup_{$upgrade_feature}"}

    {hook name="licensing:upgrade_popup"}
        <div class="hidden {if $auto_open}cm-dialog-auto-open{/if} cm-dialog-auto-size pull-left"
             id="{$popup_id}"
             title="{__("licensing.feature_not_allowed.title")}"
             data-ca-dialog-class="left"
        >
            <p>{__("licensing.feature_not_allowed.message")}</p>
        </div>
    {/hook}
{/if}
