{if $block.content.text}
    <div class="sd-cta">
        <div class="sd-cta__description">
            {$block.content.text nofilter}
        </div>

        {if $block.content.button_main_url || $block.content.button_secondary_url}
            {capture name="buttons_content"}
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
            {/capture}
            {if $block.content.buttons_only_for_registered == "YesNo::YES"|enum}
                {if $auth.user_id}
                    {$smarty.capture.buttons_content nofilter}
                {else}
                    {$return_current_url = $config.current_url|escape:url}
                    <div class="sd-cta__buttons">
                        <a href="{"profiles.add"|fn_url}" class="ty-btn ty-btn__primary" rel="nofollow">
                            {__("sd_design_changes.sign_up")}
                        </a>
                        <a href="{"auth.login_form?return_url={$return_current_url}"|fn_url}"
                            class="cm-dialog-opener cm-dialog-auto-size ty-btn ty-btn__secondary"
                            data-ca-target-id="login_block{$block.snapping_id}"
                            rel="nofollow"
                        >
                            {__("sign_in")}
                        </a>
                    </div>
                    <div id="login_block{$block.snapping_id}" class="hidden" title="{__("sign_in")}">
                        <div class="ty-login-popup">
                            {include "views/auth/login_form.tpl"
                                style="popup"
                                id="popup{$block.snapping_id}"
                            }
                        </div>
                    </div>
                {/if}
            {else}
                {$smarty.capture.buttons_content nofilter}
            {/if}
        {/if}
    </div>
{/if}
