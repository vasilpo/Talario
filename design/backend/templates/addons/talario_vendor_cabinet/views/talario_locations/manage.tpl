{capture name="mainbox"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <p class="muted">{__("talario_vendor_cabinet.locations_intro")}</p>
        <a class="btn btn-primary btn-large" href="{"talario_locations.update"|fn_url}">+ {__("talario_vendor_cabinet.add_location")}</a>
    </div>

    {if $talario_locations}
        <table class="table table-middle">
            <thead>
                <tr>
                    <th>{__("name")}</th>
                    <th>{__("address")}</th>
                    <th>{__("status")}</th>
                    <th class="right">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            {foreach $talario_locations as $location}
                <tr>
                    <td><strong>{$location.name}</strong></td>
                    <td>
                        {$location.address}
                        {if $location.address_details}<div class="muted">{$location.address_details}</div>{/if}
                    </td>
                    <td>
                        {if $location.status === "A"}
                            <span class="label label-success">{__("active")}</span>
                        {else}
                            <span class="label">{__("disabled")}</span>
                        {/if}
                    </td>
                    <td class="right nowrap">
                        <a class="btn" href="{"talario_locations.update?location_id=`$location.location_id`"|fn_url}">{__("edit")}</a>
                        <form action="{""|fn_url}" method="post" class="inline-block">
                            <input type="hidden" name="dispatch" value="talario_locations.update_status" />
                            <input type="hidden" name="location_id" value="{$location.location_id}" />
                            <input type="hidden" name="status" value="{if $location.status === "A"}D{else}A{/if}" />
                            <button type="submit" class="btn">
                                {if $location.status === "A"}{__("disable")}{else}{__("enable")}{/if}
                            </button>
                        </form>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <div class="no-items">{__("talario_vendor_cabinet.no_locations")}</div>
    {/if}
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.centers") content=$smarty.capture.mainbox}
