{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    vendor_plans => [
        id => "vendor_plans",
        label => __("vendor_plans.plan"),
        value => $company.plan|default:"–"
    ]
])}

{* Export *}
{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}