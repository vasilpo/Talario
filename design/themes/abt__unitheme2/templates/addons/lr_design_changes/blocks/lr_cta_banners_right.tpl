{if $block.content.text && $items}
    {capture name="mobile_main_banner_html"}
        {foreach $items as $item}
            {if $item@first}
                {$banner_url = $item.abt__ut2_url|default:$item.url}
                {$banner_target_blank = $item.abt__ut2_how_to_open|default:"" === "in_new_window" || $item.target == "B"}
                <div class="sd-cta-banner">
                    {if $banner_url}<a href="{$banner_url|fn_url}"{if $banner_target_blank} target="_blank"{/if}>{/if}
                        {include "common/image.tpl"
                            images=$item.abt__ut2_main_image.icon
                            image_auto_size=true
                        }
                    {if $banner_url}</a>{/if}
                </div>
            {/if}
        {/foreach}
    {/capture}

    {capture name="mobile_side_banners_html"}
        {foreach $items as $item}
            {if !$item@first}
                {$banner_url = $item.abt__ut2_url|default:$item.url}
                {$banner_target_blank = $item.abt__ut2_how_to_open|default:"" === "in_new_window" || $item.target == "B"}
                <div class="lr-cta-banners-right__side-item">
                    <div class="sd-cta-banner">
                        {if $banner_url}<a href="{$banner_url|fn_url}"{if $banner_target_blank} target="_blank"{/if}>{/if}
                            {include "common/image.tpl"
                                images=$item.abt__ut2_main_image.icon
                                image_auto_size=true
                            }
                        {if $banner_url}</a>{/if}
                    </div>
                </div>
            {/if}
        {/foreach}
    {/capture}

    <div class="sd-cta sd-cta-version-3 sd-cta-version-3-banner-hero lr-cta-banners-right{if $items|count < 2} lr-cta-banners-right--single{/if}">
        <div class="sd-cta__text">
            <div class="sd-cta__description">
                {$block.content.text|replace:"%%MOBILE_BANNERS%%":$smarty.capture.mobile_main_banner_html nofilter}
            </div>

            {if $block.content.button_main_url}
                <div class="sd-cta__buttons">
                    {if $block.content.button_main_label}
                        <span class="sd-cta-version-3__label-container">
                    {/if}
                    <a href="{$block.content.button_main_url|fn_url}" class="ty-btn ty-btn__primary">
                        {$block.content.button_main}
                    </a>
                    {if $block.content.button_main_label}
                        <span class="sd-cta-version-3__label">
                            {$block.content.button_main_label nofilter}
                        </span>
                    {/if}
                    {if $block.content.button_main_label}
                        </span>
                    {/if}
                </div>
            {/if}

            <div class="lr-cta-banners-right__mobile">
                {if $items|count > 1}
                    <div class="lr-cta-banners-right__mobile-side">
                        {$smarty.capture.mobile_side_banners_html nofilter}
                    </div>
                {/if}
            </div>
        </div>

        <div class="sd-cta__image">
            <div class="lr-cta-banners-right__media">
                <div class="lr-cta-banners-right__main">
                    {foreach $items as $item}
                        {if $item@first}
                            {$banner_url = $item.abt__ut2_url|default:$item.url}
                            {$banner_target_blank = $item.abt__ut2_how_to_open|default:"" === "in_new_window" || $item.target == "B"}
                            <div class="sd-cta-banner">
                                {if $banner_url}<a href="{$banner_url|fn_url}"{if $banner_target_blank} target="_blank"{/if}>{/if}
                                    {include "common/image.tpl"
                                        images=$item.abt__ut2_main_image.icon
                                        image_auto_size=true
                                    }
                                {if $banner_url}</a>{/if}
                            </div>
                        {/if}
                    {/foreach}
                </div>

                {if $items|count > 1}
                    <div class="lr-cta-banners-right__side">
                        {foreach $items as $item}
                            {if !$item@first}
                                <div class="lr-cta-banners-right__side-item">
                                    {$banner_url = $item.abt__ut2_url|default:$item.url}
                                    {$banner_target_blank = $item.abt__ut2_how_to_open|default:"" === "in_new_window" || $item.target == "B"}
                                    <div class="sd-cta-banner">
                                        {if $banner_url}<a href="{$banner_url|fn_url}"{if $banner_target_blank} target="_blank"{/if}>{/if}
                                            {include "common/image.tpl"
                                                images=$item.abt__ut2_main_image.icon
                                                image_auto_size=true
                                            }
                                        {if $banner_url}</a>{/if}
                                    </div>
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}
