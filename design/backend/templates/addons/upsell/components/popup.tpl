{$popup_id=$popup_id|default:"licensing_upgrade_popup_{$feature}"}

{if fn_is_expired_storage_data("upsell.{$feature}")}
    <div id="{$popup_id}" class="{if $auto_open}cm-dialog-auto-open{/if} cm-dialog-auto-height hidden" title="{__("upsell.$feature.popup_title")}">
        <div class="upsell-popup__wrapper">
            {$popup_content_pre nofilter}
            <div class="well">
                <strong>{__("upsell.$feature.popup_text")}</strong>
            </div>
            <div class="flex">
                <div class="flex-shrink-none shift-right">
                    {include_ext file="common/icon.tpl"
                        source="unlock"
                    }
                </div>
                <p class="muted">{__("upsell.how_to_unlock_notice", ['[email]' => $user_info.email]) nofilter}</p>
            </div>
            <div class="buttons-container">
                <a class="cm-ajax cm-post btn btn-primary" href="{"upsell.send_request?feature={$feature}&from_dispatch={$runtime.controller}.{$runtime.mode}"|fn_url}">{__("upsell.unlock")}</a>
            </div>
        </div>
    </div>
{else}
    <div id="{$popup_id}" class="{if $auto_open}cm-dialog-auto-open{/if} cm-dialog-auto-height hidden" title="{__("upsell.message_already_sent")}">
        <div>
            <div>
                <p>{__("upsell.message_already_sent.notice")}</p>
            </div>
            <div class="buttons-container">
                <a class="cm-dialog-closer btn btn-primary">{__("ok")}</a>
            </div>
        </div>
    </div>
{/if}
