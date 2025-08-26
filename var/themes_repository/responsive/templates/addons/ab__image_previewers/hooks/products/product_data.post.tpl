{$c_name = "add_to_cart_`$obj_id`"}
{if $details_page &&  $smarty.capture.$c_name|strpos:'checkout.add..'}
    {capture name="ab__ip_cart_button_id"}{$_but_id}{/capture}
{/if}