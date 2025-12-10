{if $block.content.text && $items}
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
    <div class="sd-cta sd-cta-version-3">
        <div class="sd-cta__text">
            <div class="sd-cta__description">
                {$block.content.text|replace:"%%MOBILE_BANNERS%%":$smarty.capture.banners_html nofilter}
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