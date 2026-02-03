{assign var="partner_vendor_id" value=$addons.exikane_changes.partner_vendor_id|default:32}
{assign var="current_vendor_id" value=$product.company_id|default:$company_id}

{if "MULTIVENDOR"|fn_allowed_for && $current_vendor_id|intval == $partner_vendor_id|intval && !$auth.user_id}
    {include file="addons/exikane_changes/components/partner_banner.tpl" banner_class="exikane-partner-banner--mobile"}
{/if}
