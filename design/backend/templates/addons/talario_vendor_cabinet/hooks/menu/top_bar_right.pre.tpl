{$talario_header_company_id = $runtime.company_id|default:$auth.company_id}
{if !$talario_header_company_id && $auth.user_id}
    {$talario_header_user = $auth.user_id|fn_get_user_info}
    {$talario_header_company_id = $talario_header_user.company_id}
{/if}
{if $talario_header_company_id}
    {$talario_header_company = $talario_header_company_id|fn_get_company_data}
    {$talario_header_center_name = $talario_header_company_id|fn_talario_vendor_cabinet_get_center_name}
{/if}
<div class="talario-partner-identity">
    <strong>{$talario_header_company.company|default:$talario_header_user.company|default:$user_info.company|default:"Партнёр"}</strong>
    <span>{$user_info.firstname|default:$talario_header_user.firstname} {$user_info.lastname|default:$talario_header_user.lastname}</span>
</div>
