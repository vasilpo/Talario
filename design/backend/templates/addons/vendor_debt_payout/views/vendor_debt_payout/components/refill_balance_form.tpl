<form id="vendor_debt_payout_refill_balance" name="refill_balance" method="post" action="{"debt.refill_balance"|fn_url}" target="_blank">
    {$amount = ""}
    {if $current_balance < 0}
        {$amount = $current_balance|abs}
    {/if}
    <div id="vendor_debt_payout_refill_amount" class="control-group hidden cm-refill-balance-block">
        <label class="control-label cm-refill-balance-label" for="elm_vendor_debt_payout_refill_balance">
            {__("vendor_debt_payout.dashboard.analytics_card.enter_an_amount")}:
        </label>
        <div class="controls">
            {include file="common/price.tpl"
            input_id="elm_vendor_debt_payout_refill_balance"
            input_name="refill_amount"
            view="input"
            class="input-full cm-refill-balance-amount"
            value=$amount
            }
        </div>
        {include file="addons/vendor_debt_payout/views/vendor_debt_payout/components/refill_balance_button.tpl"}
    </div>
    <a id="on_vendor_debt_payout_refill_amount" class="btn btn-primary cm-combination">
        {__("vendor_debt_payout.dashboard.analytics_card.refill_balance")}
    </a>
</form>