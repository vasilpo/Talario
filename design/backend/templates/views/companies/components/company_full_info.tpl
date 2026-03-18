{capture name="companies_company_full_info"}
{*
    Import
    ---
    $company
    $settings

    Local
    ---
    $company_statuses
    $company_full_description_items
    $company_full_description
    $company_full_description_items_item
*}

{$company_statuses = "companies"|fn_get_predefined_statuses:$company.status}

{$company_full_description_items = [
    company => [
        id => "company",
        label => __("name"),
        value => $company.company
    ],
    company_id => [
        id => "company_id",
        label => __("id"),
        value => $company.company_id
    ],
    email => [
        id => "email",
        label => __("email"),
        value => $company.email
    ],
    storefront => [
        id => "storefront",
        label => __("storefront_url"),
        value => $company.storefront|puny_decode,
        editions => "ULTIMATE"
    ],
    timestamp => [
        id => "timestamp",
        label => __("registered"),
        value => $company.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"
    ],
    status => [
        id => "status",
        label => ("MULTIVENDOR"|fn_allowed_for) ? __("status") : __("stores_status"),
        value => ("MULTIVENDOR"|fn_allowed_for)
            ? $company_statuses[$company.status]
            : (
                ($company.storefront_status === "StorefrontStatuses::OPEN"|enum)
                ? "ON"
                : "OFF"
            )
    ],
    status_divider => [
        id => "status_divider",
        type => "divider"
    ]
]}

{*
Hook example
---

{strip}
{$company_full_description_items = array_merge($company_full_description_items, [
    my_changes => [
        id => "my_changes",
        label => __("my_changes.label"),
        value => ($company.my_changes),
        editions => "ULTIMATE", // optional
        type => "divider" // optional
    ]
])}

{$company_full_description_items = $company_full_description_items scope=parent}
{/strip}
*}
{hook name="companies:company_full_info"}{/hook}

{* Set company full description *}
{$company_full_description = ""}
{foreach $company_full_description_items as $company_full_description_items_item}
    {* Check editions *}
    {if isset($company_full_description_items_item.editions) && !$company_full_description_items_item.editions|fn_allowed_for}
        {continue}
    {/if}

    {* Add new line *}
    {if $company_full_description !== ""}
        {$company_full_description = "`$company_full_description`\n"}
    {/if}

    {* Skip divider *}
    {if $company_full_description_items_item.type === "divider"}
        {continue}
    {/if}

    {* Merge text *}
    {$company_full_description = "`$company_full_description``$company_full_description_items_item.label`: `$company_full_description_items_item.value`"}
{/foreach}

{* Remove last empty line *}
{$company_full_description = $company_full_description|trim}

{* Export *}
{$company_full_description = $company_full_description scope=parent}
{/capture}