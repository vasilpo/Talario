{script src="js/tygh/node_cloning.js"}
{strip}
{$tag_level = $tag_level|default:"1"}
{$is_unique_id = $is_unique_id|default:true}
{$add_empty_item_on_click = ($is_unique_id) ? "Tygh.$('#box_' + this.id).cloneNode($tag_level, undefined, undefined, true);" : "Tygh.$('#box_' + this.id).cloneNode($tag_level);"}
{if $on_add}
    {$add_empty_item_on_click = "`$add_empty_item_on_click` `$on_add`"}
{/if}
{$clone_item_on_click = ($is_unique_id) ? "Tygh.$('#box_' + this.id).cloneNode($tag_level, true, undefined, true);" : "Tygh.$('#box_' + this.id).cloneNode($tag_level, true);"}
{if $only_delete != "Y"}
    <div class="btn-group">
        {if !$hide_add}
            {include file="buttons/add_empty_item.tpl" but_onclick=$add_empty_item_on_click item_id=$item_id}
        {/if}

        {if !$hide_clone}
            {include file="buttons/clone_item.tpl" but_onclick=$clone_item_on_click item_id=$item_id}
        {/if}
{/if}

    {include file="buttons/remove_item.tpl" only_delete=$only_delete but_class="cm-delete-row"}
{if $only_delete != "Y"}
    </div>
{/if}
{/strip}
