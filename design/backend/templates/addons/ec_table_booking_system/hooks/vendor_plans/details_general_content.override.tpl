<div class="control-group">
    <label class="control-label cm-required" for="elm_plan_{$id}">{__("name")}:</label>
    <div class="controls">
        <input type="text" id="elm_plan_{$id}" name="plan_data[plan]" size="35" value="{$plan.plan}" class="input-large" />
    </div>
</div>



<div class="control-group">
    <label class="control-label" for="elm_is_default_{$id}">{__("vendor_plans.best_choise")}:</label>
    <div class="controls">
        <input type="hidden" name="plan_data[is_default]" value="{if $plan.is_default || !$plans_count}1{else}0{/if}" />
        <input type="checkbox" id="elm_is_default_{$id}" name="plan_data[is_default]" size="10" value="1"{if $plan.is_default || !$plans_count} checked="checked"{/if} {if $plan.is_default || !$plans_count || $plan.status === "ObjectStatuses::DISABLED"|enum} disabled="disabled"{/if}/>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="elm_offer_booking_{$id}">{__("vendor_plans.offer_booking_system")}:</label>
    <div class="controls">
        <input type="hidden" name="plan_data[offer_booking]" value="0" />
        <input type="checkbox" id="elm_offer_booking_{$id}" name="plan_data[offer_booking]" size="10" value="1"{if $plan.offer_booking} checked="checked"{/if} />
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="elm_plan_description_{$id}">{__("description")}:</label>
    <div class="controls">
         <textarea id="elm_plan_description_{$id}"
            name="plan_data[description]"
            cols="55"
            rows="8"
            class="cm-wysiwyg input-large"
        >{$plan.description}</textarea>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="elm_position_{$id}">{__("position")}:</label>
    <div class="controls">
        <input type="text" id="elm_position_{$id}" name="plan_data[position]" size="10" value="{$plan.position}" class="input-text-short" />
    </div>
</div>

{include file="common/select_status.tpl" input_name="plan_data[status]" id="plan_data_`$id`" obj=$plan hidden=true can_be_disabled=$can_be_disabled}
