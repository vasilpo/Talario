{if $block.content.text}
    <div class="sd-cta">
        <div class="sd-cta__description">
            {$block.content.text nofilter}
        </div>

        {if $block.content.button_main_url || $block.content.button_secondary_url}
            <div class="sd-cta__buttons">
                {if $block.content.button_main_url}
                    <a href="{$block.content.button_main_url|fn_url}" class="ty-btn ty-btn__primary">
                        {$block.content.button_main}
                    </a>
                {/if}

                {if $block.content.button_secondary_url}
                    <a href="{$block.content.button_secondary_url|fn_url}" class="ty-btn ty-btn__secondary">
                        {$block.content.button_secondary}
                    </a>
                {/if}
            </div>
        {/if}
    </div>
{/if}
