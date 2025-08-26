{if $is_show_transfer_funds_button && $is_tinkoff_multiparty_payment}
    <div class="control-group">
        {include file="buttons/button.tpl"
        but_text=__("addons.tinkoff_multiparty.transfer_funds")
        but_role="action"
        but_href="tinkoff_multiparty.transfer_funds?order_id=`$order_info.order_id`&redirect_url=`$config.current_url|escape:url`"
        but_meta="btn cm-post"
        }
    </div>
{/if}

{if $is_show_cancel_funds_button && $is_tinkoff_multiparty_payment}
    <div class="control-group">
        {include file="buttons/button.tpl"
        but_text=__("addons.tinkoff_multiparty.cancel_funds")
        but_role="action"
        but_href="tinkoff_multiparty.cancel_funds?order_id=`$order_info.order_id`&redirect_url=`$config.current_url|escape:url`"
        but_meta="btn cm-post"
        }
    </div>
{/if}