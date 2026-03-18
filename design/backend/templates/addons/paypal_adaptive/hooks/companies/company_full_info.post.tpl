{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    paypal_adaptive => [
        id => "paypal_adaptive",
        label => __("paypal_verification"),
        value => __($company.paypal_verification)
    ]
])}

{* Export *}
{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}