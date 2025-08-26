{if $ab__motivation_items}
    {hook name="ab__motivation_block:block"}
    {assign var="id" value=$ab__mb_id|default:"ab__mb_id_`$block.block_id`_`$product.product_id`"}
        <div class="ab__motivation_block ab__{$addons.ab__motivation_block.template_variant}{if $ab__motivation_items} loaded{/if}" data-ca-product-id="{$product.product_id}" data-ca-result-id="{$id}" style="
        {if $addons.ab__motivation_block.bg_color !== "#ffffff"}
                --ab-mb-bg-color: {$addons.ab__motivation_block.bg_color};
            {if $addons.ab__motivation_block.use_contrast_style_elements === "YesNo::YES"|enum}--ab-mb-items-color: #ffffff;{/if}
        {/if}">
            <div id="{$id}">
                <div class="ab__mb_items {$addons.ab__motivation_block.appearance_type_styles}{if $addons.ab__motivation_block.bg_color != "#ffffff"} colored{/if}{if $addons.ab__motivation_block.use_contrast_style_elements === "YesNo::YES"|enum} darken{else} lighten{/if}{if $runtime.customization_mode.live_editor} ab-mb-live-editor{/if}">
                    {include file="addons/ab__motivation_block/blocks/components/`$addons.ab__motivation_block.template_variant`.tpl"}
                </div>
                <!--{$id}--></div>
        </div>
    {/hook}
{/if}
