{if $show_add_to_wishlist}
    <div class="ty-valign-top">
        {include file="addons/wishlist/views/wishlist/components/add_to_wishlist.tpl" but_id="button_wishlist_`$obj_id`" but_name="dispatch[wishlist.add..`$obj_id`]" but_role="text" hidden_label=true hidden_but_label=true but_title=false but_tooltip=true}
    </div>
{/if}
