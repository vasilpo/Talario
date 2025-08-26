<div id="refunds_management">
    <h4 class="subheader">{__("order")}:# {$order_id} [{$gateway_order_id}] </h4>

    <div class="foo" data-ca-bar="bar">
        {if $result}{$result}{/if}
        <!--div_id_to_be_updated--></div>

    <form action="{""|fn_url}"
          method="post"
          class="form-horizontal idabi">

{*        <input type="hidden" id="gateway-action" name="gateway_action">*}
        <input type="hidden" class="form-control" value="{$order_id}" name="order_id">
    <table id="alfabank-table" class="table table-bordered">
    <tbody>

<!-- todo MOVE it inside action like other buttons!! -->

    <tr id="input-fields">
        <td class="text-right col-sm-2">
            {__("addons.alfabank.gateway_payment_status")}:
        </td>
        <td class="text-left">
            <div class="col-sm-6" id="div_id_to_be_updated">
                <input type="button" class="btn-link idabi-link" data-action="check_gateway_status" value="{__("addons.alfabank.btn_check_status")}"/>
            </div>
        </td>
    </tr>


    {if ($gateway_order_status == 'DEPOSITED' || $gateway_order_status == 'REFUNDED') && $order_info.payment_info.gateway_deposited > 0}
        <!-- DEPOSITED > REFUNDED -->
        <tr id="input-fields">
            <td class="text-right col-sm-2">
                {__("addons.alfabank.gateway_payment_refund")}:
            </td>
            <td class="text-left">
                <div class="col-sm-6">
                    <div class="input-group">

                        <input type="text" class="form-control" value="{$order_info['payment_info']['gateway_deposited']}" name="amount" id="user_amount" autofocus="autofocus">
                        <span class="input-group-btn" style="margin-left:-5px">
                        <button class="btn btn-warning btn-idabi" name="dispatch[alfabank.payment_refund.partial]"
                                type="submit">{__("addons.alfabank.btn_refund")}</button>
                            <button class="btn btn-warning btn-idabi" name="dispatch[alfabank.payment_refund]"
                                    type="submit">{__("addons.alfabank.btn_refund_full")}</button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-12">
                    Min amount: "0.01", Max amount: "{$order_info.payment_info.gateway_deposited}"
                </div>

            </td>
        </tr>
    {/if}

    {if $gateway_order_status == 'APPROVED'}
        <!-- APPROVED -->
        <tr id="input-fields">
            <td class="text-right col-sm-2">
                {__("addons.alfabank.gateway_payment_deposit")}:
            </td>
            <td class="text-left">
                <div class="col-sm-6">
                    <div class="input-group">

                        <input type="text" class="form-control" value="{$order_info['payment_info']['gateway_approved']}" name="amount" id="user_amount" autofocus="autofocus">

                        <span class="input-group-btn" style="margin-left:-5px">
                        <button class="btn btn-warning btn-idabi" name="dispatch[alfabank.payment_deposit.partial]"
                                type="submit">{__("addons.alfabank.btn_deposit")}</button>
                            <button class="btn btn-warning btn-idabi" name="dispatch[alfabank.payment_deposit]"
                                    type="submit">{__("addons.alfabank.btn_deposit_full")}</button>
                        </span>
                    </div>
                </div>

                <div class="col-sm-12">
                    Min amount: "0.01", Max amount: "{$order_info.payment_info.gateway_approved}"
                </div>

            </td>
        </tr>
    {/if}

    <!-- ------- -->
    <tr id="input-fields">
        <td class="text-right col-sm-2">
            {__("addons.alfabank.gateway_payment_reverse")}:
        </td>
        <td class="text-left">
            <div class="col-sm-6">
                <button class="btn btn-warning btn-idabi" name="dispatch[alfabank.payment_reverse]"
                        type="submit">{__("addons.alfabank.btn_reverse")}</button>
            </div>
        </td>
    </tr>


    </tbody>
    </table>

        <div class="buttons-container">
            {include file="addons/alfabank/components/close_popup.tpl"}
        </div>
    </form>

</div>


<script type="text/javascript">

    $(document).on("click", '.idabi-link',function(){

        $.ceAjax('request', fn_url('alfabank.doAction'), {
            data: {
                order_id: "{$order_id}",
                action: $(this).attr("data-action"),
                amount: $("#user_amount").val()

            },
            method: 'post',
            callback: function callback(response) {
                var alertHTML = response.result;
                $("#div_id_to_be_updated").html(alertHTML);
            }
        });

    })

</script>