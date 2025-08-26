<div class="control-group setting-wide" id="ec_booking_color">
    <label class="control-label cm-color">{__("ec_booking.color_unavailable")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="ec_settings[unavailable]" cp_id="status_param_unavailable" cp_value=$ec_settings.unavailable show_picker=true}
    </div>
</div>

<div class="control-group setting-wide" id="ec_booking_color_free">
    <label class="control-label cm-color">{__("ec_booking.color_free")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="ec_settings[free]" cp_id="status_param_free" cp_value=$ec_settings.free show_picker=true}
    </div>
</div>

<div class="control-group setting-wide" id="ec_booking_color_reserved">
    <label class="control-label cm-color">{__("ec_booking.color_reserved")}:</label>
    <div class="controls">
        {include file="common/colorpicker.tpl" cp_name="ec_settings[reserved]" cp_id="status_param_reserved" cp_value=$ec_settings.reserved show_picker=true}
    </div>
</div>

<div class="control-group setting-wide" id="ec_booking_time_format">
    <label class="control-label cm-color">{__("ec_booking.time_format")}:</label>
    <div class="controls">
        <select name="ec_settings[time_format]">
            <option value="12" {if (isset($ec_settings.time_format) && $ec_settings.time_format == "12")}selected="selected"{/if}>{__("ec_booking_12_hour_format")}</option>
            <option value="24" {if (isset($ec_settings.time_format) && $ec_settings.time_format == "24")}selected="selected"{/if}>{__("ec_booking_24_hour_format")}</option>
        </select>
    </div>
</div>

<div class="control-group setting-wide" id="ec_booking_week_start">
    <label class="control-label cm-color">{__("ec_booking.week_start")}:</label>
    <div class="controls">
        <select name="ec_settings[week_start]">
            <option value="sunday" {if (isset($ec_settings.week_start) && $ec_settings.week_start == "sunday")}selected="selected"{/if}>{__("ec_booking_sunday")}</option>
            <option value="monday" {if (isset($ec_settings.week_start) && $ec_settings.week_start == "monday")}selected="selected"{/if}>{__("ec_booking_monday")}</option>
        </select>
    </div>
</div>

{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
<div class="control-group setting-wide" >	
    <label class="control-label" for="ec_booking_delete_status">{__("ec_booking.delete_status")}:</label>	
    <div class="controls">		
        <select name="ec_settings[cancel_status]" id="ec_booking_delete_status">			
            {foreach from=$statuses item="s" key="k"}			
                <option value="{$k}" {if (isset($ec_settings.cancel_status) && $ec_settings.cancel_status == $k)}selected="selected"{/if}>{$s}</option>			
            {/foreach}		
        </select>	
    </div>
</div>

{assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
<div class="control-group setting-wide" >	
    <label class="control-label" for="ec_booking_approve_status">{__("ec_booking.approve_status")}:</label>	
    <div class="controls">		
        <select name="ec_settings[approve_status]" id="ec_booking_approve_status">			
            {foreach from=$statuses item="s" key="k"}			
                <option value="{$k}" {if (isset($ec_settings.approve_status) && $ec_settings.approve_status == $k)}selected="selected"{/if}>{$s}</option>			
            {/foreach}		
        </select>	
    </div>
</div>