<div id="content_ofd_ferma">
    <div class="control-group">
        <input type="hidden" name="order_ids[]" value="{$order_info.order_id}" />

        <div>
            <div style='display:inline-block;'>
                {include file="buttons/button.tpl" but_text=__("btn_order_income") but_meta="cm-no-ajax" but_target_form="order_info_form" but_name="dispatch[orders.income_ofd_ferma]" }
            </div>&nbsp;
            <div style='display:inline-block;'>
                {include file="buttons/button.tpl" but_text=__("btn_order_income_return") but_meta="cm-no-ajax" but_target_form="order_info_form" but_name="dispatch[orders.income_return_ofd_ferma]" }
            </div>
        </div>
    </div>
</div>