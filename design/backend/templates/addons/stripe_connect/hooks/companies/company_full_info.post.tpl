{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    stripe_connect => [
        id => "stripe_connect",
        label => __("stripe_connect.stripe_connect"),
        value => $company.stripe_connect_account_id|default:"–"
    ]
])}

{* Export *}
{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}