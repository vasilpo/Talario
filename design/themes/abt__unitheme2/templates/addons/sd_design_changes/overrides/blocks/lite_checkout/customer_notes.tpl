<!-- The "{$smarty.template}" template was overriden by the "sd_design_changes" add-on -->
<div class="litecheckout__container">
    {include file="views/checkout/components/customer_notes.tpl"
        field_name=__("sd_design_changes.provide_child_name")|default:$block.name
    }

    <p class="ty-muted">
        {__("sd_design_changes.provide_child_name_text")}
    </p>
</div>
