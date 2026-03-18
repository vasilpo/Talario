{$show_feature_header = $show_feature_header|default:false}
{$product_features_threshold = $product_features_threshold|default:5}
{$feature_iteration = $feature_iteration|default:0}

{if $feature_iteration !== $product_features_threshold}
    {foreach $product_features as $feature}
        {if $feature.feature_type != "ProductFeatures::GROUP"|enum}
            {if $feature_iteration === $product_features_threshold}
                {break}
            {/if}

            {include_ext file="common/icon.tpl" class="ty-icon-help-circle" assign=link_text_icon}
            <div class="ty-product-features-short-table">
            <div class="ty-product-features-short-table__label">{$feature.description nofilter}{if $feature.full_description|trim}{include file="common/help.tpl" text=$feature.description content=$feature.full_description id="`$feature.feature_id`_short" show_brackets=false link_text="<span class=\"ty-tooltip-block\">`$link_text_icon`</span>" wysiwyg=true}{/if}:</div>

            {$hide_affix = $feature.feature_type == "ProductFeatures::MULTIPLE_CHECKBOX"|enum}

            {strip}
            <div class="ty-product-features-short-table__value">
                {if $feature.prefix && !$hide_affix}<span class="ty-product-features-short-table__prefix">{$feature.prefix}</span>{/if}
                {if $feature.feature_type == "ProductFeatures::SINGLE_CHECKBOX"|enum}
                <span class="ty-compare-checkbox">{if $feature.value === "YesNo::YES"|enum}{include_ext file="common/icon.tpl" class="ty-icon-ok ty-compare-checkbox__icon"}{/if}
                </span>
                {elseif $feature.feature_type == "ProductFeatures::DATE"|enum}
                    {$feature.value_int|date_format:"`$settings.Appearance.date_format`"}
                {elseif $feature.feature_type == "ProductFeatures::MULTIPLE_CHECKBOX"|enum && $feature.variants}
                    <ul class="ty-product-features-short-table__multiple">
                    {foreach from=$feature.variants item="var"}
                        {$hide_variant_affix = !$hide_affix}
                        {if $var.selected}<li class="ty-product-features-short-table__multiple-item"><span class="ty-compare-checkbox">{include_ext file="common/icon.tpl" class="ty-icon-ok ty-compare-checkbox__icon"}</span>{if !$hide_variant_affix}<span class="ty-product-features-short-table__prefix">{$feature.prefix}</span>{/if}{$var.variant}{if !$hide_variant_affix}<span class="ty-product-features-short-table__suffix">{$feature.suffix}</span>{/if}</li>{/if}
                    {/foreach}
                    </ul>
                {elseif in_array($feature.feature_type, ["ProductFeatures::TEXT_SELECTBOX"|enum, "ProductFeatures::EXTENDED"|enum, "ProductFeatures::NUMBER_SELECTBOX"|enum])}
                    {foreach $feature.variants as $variant}
                        {if $variant.selected}{$variant.variant}{break}{/if}
                    {/foreach}
                {elseif $feature.feature_type == "ProductFeatures::NUMBER_FIELD"|enum}
                    {$feature.value_int|floatval|default:"-"}
                {else}
                    {$feature.value|default:"-"}
                {/if}
                {if $feature.suffix && !$hide_affix}<span class="ty-product-features-short-table__suffix">{$feature.suffix}</span>{/if}
            </div>
            {/strip}
            </div>
            {$feature_iteration = $feature_iteration + 1}
        {/if}
    {/foreach}

    {foreach $product_features as $feature}
    {if $feature_iteration !== $product_features_threshold}
        {if $feature.feature_type == "ProductFeatures::GROUP"|enum && $feature.subfeatures}
            {if $show_feature_header}
                <div class="ty-product-features-short-table-group">
                    {include file="common/subheader.tpl" title=$feature.description tooltip=$feature.full_description text=$feature.description subheader_tag="div"}
                    {include file="views/products/components/product_features_short_table.tpl" product_features=$feature.subfeatures feature_iteration=$feature_iteration}
                </div>
            {else}
                {include file="views/products/components/product_features_short_table.tpl" product_features=$feature.subfeatures feature_iteration=$feature_iteration}
            {/if}
        {/if}
    {/if}
    {/foreach}
{/if}
{$feature_iteration = $feature_iteration scope=parent}
