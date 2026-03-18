{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    paypal_commerce_platform => [
        id => "paypal_commerce_platform",
        label => __("paypal_commerce_platform.paypal_account"),
        value => $company.paypal_commerce_platform_account_id|default:__("paypal_commerce_platform.not_connected")
    ]
])}

{* Export *}
{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}