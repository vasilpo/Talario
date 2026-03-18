{if $company.plan}{strip}
    <small title="{$company_full_description}">
        {$company.plan|truncate:20:"...":true}
    </small>
{/strip}{/if}