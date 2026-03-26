{$excluded_dispatches = ['profiles.update', 'orders.search', 'product_features.compare', 'vendor_communication.threads', 'reward_points.userlog', 'wishlist.view', 'orders.details']}

{if $block.content.text && !in_array($smarty.request.dispatch, $excluded_dispatches) && $exception_status != "404"}
    <div class="sd-cta sd-cta-version-2">
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
            </div>
        {/if}
    </div>
{/if}
