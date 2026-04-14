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
    {capture name="banners_html"}
        {if $items}
            {foreach $items as $item}
                <div class="sd-cta-banner">
                    {if $item.url}<a href="{$item.url|fn_url}"{if $item.target == "B"} target="_blank"{/if}>{/if}
                        {include "common/image.tpl"
                            images=$item.abt__ut2_main_image.icon
                            image_auto_size=true
                        }
                    {if $item.url}</a>{/if}
                </div>
            {/foreach}
        {/if}
    {/capture}
    <div class="sd-cta sd-cta-version-3 sd-cta-version-3-banner-hero">
        <div class="sd-cta__text">
            <div class="sd-cta__description">
                {$cta_text|replace:"%%MOBILE_BANNERS%%":$smarty.capture.banners_html nofilter}
            </div>
            {if !$auth.user_id}
                {include file="design/themes/abt__unitheme2/templates/addons/exikane_changes/components/guest_banner.tpl"
                    banner_title=__("exikane_changes.guest_banner_title")
                    banner_text=__("exikane_changes.guest_banner_text")
                }
            {/if}

            {if $cta_button_url}
                <div class="sd-cta__buttons">
                    {if $cta_button_label}
                        <span class="sd-cta-version-3__label-container">
                    {/if}
                     <a href="javascript:void(0);" class="ty-btn ty-btn__primary cm-scroll" data-ca-scroll="#homepage_catalog">
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
        </div>

        <div class="sd-cta__image">
            {foreach $items as $item}
                <div class="sd-cta-banner">
                    {if $item.url}<a href="{$item.url|fn_url}"{if $item.target == "B"} target="_blank"{/if}>{/if}
                        {include "common/image.tpl"
                            images=$item.abt__ut2_main_image.icon
                            image_auto_size=true
                        }
                    {if $item.url}</a>{/if}
                </div>
            {/foreach}
        </div>
    </div>
{/if}
