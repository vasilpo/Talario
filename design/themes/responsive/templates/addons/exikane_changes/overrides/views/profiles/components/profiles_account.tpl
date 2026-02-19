{if !$nothing_extra}
    {include file="common/subheader.tpl" title=__("user_account_info")}
{/if}

{hook name="profiles:account_info"}
    <div class="ty-control-group">
        <label for="email" class="ty-control-group__title cm-required cm-email cm-trim">{__("email")}</label>
        <input type="text" id="email" name="user_data[email]" size="32" maxlength="128" value="{$user_data.email}" class="ty-input-text cm-focus" />
    </div>

    <div class="ty-control-group">
        <label for="password1" class="ty-control-group__title cm-required cm-password">{__("password")}</label>
        <div class="exikane-password-wrap">
            <input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}" class="ty-input-text cm-autocomplete-off" data-ca-password-toggle-field="true" />
            {if !$auth.user_id}
                <button type="button" class="exikane-password-toggle cm-exikane-password-toggle" aria-label="{__("exikane_changes.show_password")}" aria-pressed="false">
                    <i class="ty-icon-eye-open"></i>
                </button>
            {/if}
        </div>
    </div>

    <div class="ty-control-group">
        <label for="password2" class="ty-control-group__title cm-required cm-password">{__("confirm_password")}</label>
        <div class="exikane-password-wrap">
            <input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="{if $runtime.mode == "update"}            {/if}" class="ty-input-text cm-autocomplete-off" data-ca-password-toggle-field="true" />
            {if !$auth.user_id}
                <button type="button" class="exikane-password-toggle cm-exikane-password-toggle" aria-label="{__("exikane_changes.show_password")}" aria-pressed="false">
                    <i class="ty-icon-eye-open"></i>
                </button>
            {/if}
        </div>
    </div>
{/hook}
