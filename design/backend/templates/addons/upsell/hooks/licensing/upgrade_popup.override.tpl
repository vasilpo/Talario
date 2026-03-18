{$feature = $upgrade_feature}

{if fn_is_lang_var_exists("upsell.$feature.popup_title") && $settings.Upgrade_center.license_number}
    {include file="addons/upsell/components/popup.tpl" feature=$feature popup_id=$popup_id auto_open=$auto_open}
{else}
    <div class="hidden {if $auto_open}cm-dialog-auto-open{/if} cm-dialog-auto-size pull-left"
         id="{$popup_id}"
         title="{__("licensing.feature_not_allowed.title")}"
         data-ca-dialog-class="left"
    >
        <p>{__("licensing.feature_not_allowed.message")}</p>
    </div>
{/if}
