{if $runtime.company_id}
    {$talario_header_center_name = $runtime.company_id|fn_talario_vendor_cabinet_get_center_name}
    <div class="talario-partner-identity" title="{$runtime.company_data.company}{if $talario_header_center_name} ({$talario_header_center_name}){/if}">
        <strong>{$runtime.company_data.company}</strong>
        {if $talario_header_center_name}<span> ({$talario_header_center_name})</span>{/if}
    </div>
{/if}
