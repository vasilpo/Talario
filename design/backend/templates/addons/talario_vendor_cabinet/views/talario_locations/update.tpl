{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="talario_location_form" class="form-horizontal form-edit">
    <input type="hidden" name="dispatch" value="talario_locations.update" />
    {if $talario_location.location_id}
        <input type="hidden" name="location_id" value="{$talario_location.location_id}" />
    {/if}

    <div class="control-group">
        <label class="control-label cm-required" for="elm_talario_location_name">{__("name")}:</label>
        <div class="controls">
            <input type="text" id="elm_talario_location_name" name="location_data[name]" value="{$talario_location.name}" class="input-large" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label cm-required" for="elm_talario_location_address">{__("address")}:</label>
        <div class="controls">
            <input type="text" id="elm_talario_location_address" name="location_data[address]" value="{$talario_location.address}" class="input-xlarge" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_talario_location_details">{__("talario_vendor_cabinet.address_details")}:</label>
        <div class="controls">
            <textarea id="elm_talario_location_details" name="location_data[address_details]" rows="4" class="input-xlarge">{$talario_location.address_details}</textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="elm_talario_location_status">{__("status")}:</label>
        <div class="controls">
            <select id="elm_talario_location_status" name="location_data[status]">
                <option value="A" {if $talario_location.status === "A"}selected="selected"{/if}>{__("active")}</option>
                <option value="D" {if $talario_location.status === "D"}selected="selected"{/if}>{__("disabled")}</option>
            </select>
        </div>
    </div>

    <div class="buttons-container">
        <a class="btn" href="{"talario_locations.manage"|fn_url}">{__("cancel")}</a>
        <button type="submit" class="btn btn-primary">{__("save")}</button>
    </div>
</form>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.location") content=$smarty.capture.mainbox}
