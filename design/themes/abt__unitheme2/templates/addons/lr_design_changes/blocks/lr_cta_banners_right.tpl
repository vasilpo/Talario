{assign var="cta_text" value=$block.content.text}
{assign var="cta_button_text" value=$block.content.button_main}
{assign var="cta_button_url" value=$block.content.button_main_url}
{assign var="cta_button_label" value=$block.content.button_main_label}

{if $auth.user_id}
    {if $block.content.registered_text}
        {assign var="cta_text" value=$block.content.registered_text}
    {/if}
    {assign var="cta_button_text" value=$block.content.registered_button_main}
    {assign var="cta_button_url" value=$block.content.registered_button_main_url}
    {assign var="cta_button_label" value=$block.content.registered_button_main_label}
{/if}

{if $cta_text && $items}
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
                {$cta_text|replace:"%%MOBILE_BANNERS%%":$smarty.capture.mobile_main_banner_html nofilter}
            </div>

            {if $cta_button_url}
                <div class="sd-cta__buttons">
                    {if $cta_button_label}
                        <span class="sd-cta-version-3__label-container">
                    {/if}
                    <a href="{$cta_button_url|fn_url}" class="ty-btn ty-btn__primary">
                        {$cta_button_text}
                    </a>
                    {if $cta_button_label}
                        <span class="sd-cta-version-3__label">
                            {$cta_button_label nofilter}
                        </span>
                    {/if}
                    {if $cta_button_label}
                        </span>
                    {/if}
                </div>
            {/if}

            {if !$auth.user_id}
                <div class="lr-cta-banners-right__mobile">
                    {if $items|count > 1}
                        <div class="lr-cta-banners-right__mobile-side">
                            {$smarty.capture.mobile_side_banners_html nofilter}
                        </div>
                    {/if}
                </div>
            {/if}
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

                {if !$auth.user_id && $items|count > 1}
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
