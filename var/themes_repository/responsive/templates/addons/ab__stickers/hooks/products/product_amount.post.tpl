{* Workaround for master_products addon*}
{* master_products addon rewrite obj_id so we need to store it for next use *}
{if $is_allow_add_common_products_to_cart_list}
    {assign var="ab__stickers_obj_id" value=$obj_id scope="parent"}
{/if}
