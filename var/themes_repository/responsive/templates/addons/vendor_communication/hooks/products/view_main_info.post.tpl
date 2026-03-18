{$show_product_company_data = $show_product_company_data|default:true}
{if $addons.vendor_communication.show_on_product == "Y" && $show_product_company_data}
    <div class="hidden" id="product_vendor_communication_thread_form{$obj_prefix}{$obj_id}">
    {if $auth.user_id}
        {include
            file="addons/vendor_communication/views/vendor_communication/components/new_thread_form.tpl"
            object_type=$smarty.const.VC_OBJECT_TYPE_PRODUCT
            object_id=$product.product_id
            company_id=$product.company_id
            vendor_name=$product.company_name
        }
    {else}
        {include file="addons/vendor_communication/views/vendor_communication/components/login_form.tpl"}
    {/if}
    <!--product_vendor_communication_thread_form{$obj_prefix}{$obj_id}--></div>
{/if}