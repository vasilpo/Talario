{if $catalog_data}
    <div id="{$catalog_data.target_id}">
        {include file="addons/lr_design_changes/components/homepage_catalog_content.tpl" catalog_data=$catalog_data}
    <!--{$catalog_data.target_id}--></div>
{/if}
