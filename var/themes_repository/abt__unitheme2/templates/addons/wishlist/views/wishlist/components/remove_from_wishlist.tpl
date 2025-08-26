{if $is_wishlist}
     <a href="{"wishlist.delete?cart_id=`$product.cart_id`"|fn_url}" class="ut2-add-to-wish ty-remove cm-tooltip" title="{__("remove")}">{include_ext file="common/icon.tpl" class="ut2-icon-highlight_off"}</a>
{/if}