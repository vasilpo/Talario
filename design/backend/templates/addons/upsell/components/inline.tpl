{if fn_upsell_is_upsellable($feature)}
    <div class="upsell-inline__wrapper">
        <div class="upsell-inline">
            {if fn_is_expired_storage_data("upsell.{$feature}")}
                {$promo_content_pre nofilter}
                <div class="well">
                    <strong>{__("upsell.$feature.promo_text")}</strong>
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
            {else}
                <p>{__("upsell.message_already_sent.notice")}</p>
            {/if}
        </div>
    </div>
{/if}
