{capture name="select_supplier"}
    {include file="addons/suppliers/views/suppliers/components/select_supplier.tpl"}
{/capture}

{if $smarty.capture.select_supplier && $smarty.capture.select_supplier|trim !== ""}
    {$search_filters.data.select_supplier = [
        id => "select_supplier",
        type => "popup",
        category => "secondary",
        label => __("search_by_supplier"),
        content => $smarty.capture.select_supplier
    ]}
{/if}

{* Export *}
{$search_filters = $search_filters scope=parent}
