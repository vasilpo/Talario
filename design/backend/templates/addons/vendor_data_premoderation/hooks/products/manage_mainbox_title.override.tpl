{if $_REQUEST
    && $_REQUEST['dispatch']
    && $_REQUEST['dispatch'] === "products.manage"
    && $_REQUEST['status']
    && (
        $_REQUEST['status'] === "Addons\VendorDataPremoderation\ProductStatuses::REQUIRES_APPROVAL"|enum
        || $_REQUEST['status'] === "Addons\VendorDataPremoderation\ProductStatuses::DISAPPROVED"|enum
    )
}
    {if $_REQUEST['status'] === "Addons\VendorDataPremoderation\ProductStatuses::REQUIRES_APPROVAL"|enum}
        {__("vendor_data_premoderation.require_approval")}
    {elseif $_REQUEST['status'] === "Addons\VendorDataPremoderation\ProductStatuses::DISAPPROVED"|enum}        
        {__("vendor_data_premoderation.require_vendor_action")}
    {/if}
{/if}
