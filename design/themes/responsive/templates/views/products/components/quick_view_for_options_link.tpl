{if $smarty.request.redirect_url}
    {$current_url = $smarty.request.redirect_url|urlencode}
{else}
    {$current_url = $config.current_url|urlencode}
{/if}
{capture name="quick_view_url"}
{** Sets quick view for options link *}
{hook name="products:product_quick_view_url"}
{"products.quick_view?product_id=`$product.product_id`&show_quick_view_for_options=Y&prev_url=`$current_url`"}
{/hook}
{/capture}
{$quick_view_url = $smarty.capture.quick_view_url|trim}
{if $block.type && $block.type != 'main'}
    {$quick_view_url = $quick_view_url|fn_link_attach:"n_plain=Y"}
{/if}
{if $quick_nav_ids}
    {$quick_nav_ids = ","|implode:$quick_nav_ids}
    {$quick_view_url = $quick_view_url|fn_link_attach:"n_items=`$quick_nav_ids`"}
{/if}

{hook name="products:product_quick_view_microstore"}{/hook}

<a class="ty-btn ty-btn__primary ty-btn__big ty-btn__add-to-cart cm-dialog-opener cm-dialog-auto-size ty-quick-view-for-options-link {$quick_view_for_options_link_class}"
    data-ca-view-id="{$product.product_id}_for_options"
    data-ca-target-id="product_quick_view_for_options"
    href="{$quick_view_url|fn_url}"
    data-ca-dialog-title="{__("add_to_cart")}"
    rel="nofollow"
>{include_ext file="common/icon.tpl" class=$quick_view_icon}{$quick_view_text|default:__("add_to_cart")}</a>