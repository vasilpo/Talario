{capture name="buttons"}
    {include file="buttons/button.tpl"
        but_role="action"
        but_text=__("partner_sites.partner_site_clicks_export")
        but_href="partner_sites.partner_site_clicks_export"|fn_url
    }
{/capture}

{capture name="mainbox"}
    {if $clicks}
        <div class="table-responsive-wrapper">
            <table class="table table-middle table--relative table-responsive">
                <thead>
                <tr>
                    <th>{__("partner_sites.partner_site_clicks_user_id")}</th>
                    <th>{__("partner_sites.partner_site_clicks_email")}</th>
                    <th>{__("partner_sites.partner_site_clicks_product_id")}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$clicks item=click}
                    {$product_url = "products.update?product_id=`$click.product_id`"}
                    <tr>
                        <td data-th="{__("partner_sites.partner_site_clicks_user_id")}">
                            <a href="{$product_url|fn_url}">{$click.user_id}</a>
                        </td>
                        <td data-th="{__("partner_sites.partner_site_clicks_email")}">
                            {$click.email|default:"-"}
                        </td>
                        <td data-th="{__("partner_sites.partner_site_clicks_product_id")}">
                            <a href="{$product_url|fn_url}">{$click.product_id}</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

        {include file="common/pagination.tpl" search=$search}
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}
{/capture}

{include file="common/mainbox.tpl"
    title=__("partner_sites.partner_site_clicks_title")
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
}
