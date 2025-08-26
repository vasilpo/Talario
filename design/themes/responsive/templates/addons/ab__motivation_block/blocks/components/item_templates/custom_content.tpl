<div class="ty-wysiwyg-content{if $addons.ab__motivation_block.use_style_presets == {"YesNo::YES"|enum}} ab-mb-style-presets{/if}">
    {if $addons.ab__motivation_block.description_type == 'smarty' && !$is_vendor_description}
        {eval_string var=$ab__mb_item.description}
    {else}
        {$ab__mb_item.description nofilter}
    {/if}
</div>