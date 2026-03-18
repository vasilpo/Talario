{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    vendor_rating => [
        id => "vendor_rating",
        label => __("vendor_rating.absolute_vendor_rating"),
        value => $company.absolute_vendor_rating|default:"–"
    ]
])}

{* Export *}
{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}