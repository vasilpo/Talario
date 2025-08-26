{if $_REQUEST
    && $_REQUEST['dispatch']
    && $_REQUEST['dispatch'] === "companies.manage"
    && $_REQUEST['status']
    && $_REQUEST['status'][0] === "Addons\VendorDataPremoderation\PremoderationStatuses::PENDING"|enum
    && $_REQUEST['status'][1] === "Addons\VendorDataPremoderation\PremoderationStatuses::DISAPPROVED"|enum
}
    {__("vendor_data_premoderation.vendors_require_approval")}
{/if}
