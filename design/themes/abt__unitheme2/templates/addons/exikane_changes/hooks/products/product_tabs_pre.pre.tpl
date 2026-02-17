{assign var="partner_vendor_id" value=$addons.exikane_changes.partner_vendor_id|default:32}
{assign var="current_vendor_id" value=$product.company_id|default:$company_id}
{assign var="is_partner_vendor" value="MULTIVENDOR"|fn_allowed_for && $current_vendor_id|intval == $partner_vendor_id|intval}

{if !$auth.user_id && !$is_partner_vendor}
    {include file="addons/exikane_changes/components/guest_banner.tpl"
        banner_title=__("exikane_changes.guest_banner_title_product_page")
        banner_class="exikane-guest-banner--compact"
    }
{/if}
