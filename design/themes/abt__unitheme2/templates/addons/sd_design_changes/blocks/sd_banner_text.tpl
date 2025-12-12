{if $items}
    {foreach $items as $item}
        <div class="sd-banner-text"{if $item.abt__ut2_description_bg_color_use} style="--bg-color: {$item.abt__ut2_description_bg_color}"{/if}>
            <div class="sd-banner-text__image">
                <div class="sd-banner-text-banner">
                    {include "common/image.tpl"
                        images=$item.abt__ut2_main_image.icon
                        image_auto_size=true
                    }
                </div>
            </div>

            <div class="sd-banner-text__description">
                <p class="sd-banner-text__description-title" style="--fz: {$item.abt__ut2_title_font_size};--weight: {$item.abt__ut2_title_font_weight}; --color: {$item.abt__ut2_title_color};">
                    {$item.abt__ut2_title nofilter}
                </p>

                {if $item.abt__ut2_description}
                    <div class="sd-banner-text__description-text" style="--fz: {$item.abt__ut2_description_font_size};--color: {$item.abt__ut2_description_color};">
                        {$item.abt__ut2_description nofilter}
                    </div>
                {/if}
            </div>
        </div>
        {break}
    {/foreach}
{/if}