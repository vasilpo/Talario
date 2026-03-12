{ab__hide_content bot_type="ALL"}
{assign var="id" value=$id|default:"main_login"}
{assign var="is_popup_login" value=$style === "popup"}

{capture name="login"}
    <form name="{$id}_form" action="{""|fn_url}" method="post" {if $is_popup_login}class="cm-ajax cm-ajax-full-render exikane-login-popup__form"{/if}>
        {if $style == "popup"}
            <input type="hidden" name="result_ids" value="{$id}_login_popup_form_container" />
            <input type="hidden" name="login_block_id" value="{$id}" />
            <input type="hidden" name="quick_login" value="1" />
        {/if}

        <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}" />
        <input type="hidden" name="redirect_url" value="{$redirect_url|default:$config.current_url}" />

        {if $style == "checkout"}
            <div class="ty-checkout-login-form">{include file="common/subheader.tpl" title=__("returning_customer")}
        {/if}

        {if $is_popup_login}
            <div class="exikane-login-popup">
                <div class="exikane-login-popup__header">
                    <h2 class="exikane-login-popup__title">{__("exikane_changes.login_popup_title")}</h2>
                    <p class="exikane-login-popup__subtitle">{__("exikane_changes.login_popup_subtitle")}</p>
                </div>

                {include
                    file="addons/exikane_changes/components/guest_banner.tpl"
                    banner_class="exikane-guest-banner--login-popup"
                    banner_title=__("exikane_changes.login_popup_bonus_title")
                    banner_href={"profiles.add"|fn_url}
                }

                {if $login_error}
                    <div class="ty-login-form__wrong-credentials-container exikane-login-popup__error">
                        <span class="ty-login-form__wrong-credentials-text ty-error-text">{__("error_incorrect_login")}</span>
                    </div>
                {/if}

                <div class="ty-control-group exikane-login-popup__control-group">
                    <label for="login_{$id}" class="ty-login__filed-label ty-control-group__label cm-required cm-trim cm-email">{__("email")}</label>
                    <input
                        type="text"
                        id="login_{$id}"
                        name="user_login"
                        size="30"
                        value="{if $stored_user_login}{$stored_user_login}{else}{$config.demo_username}{/if}"
                        class="ty-login__input cm-focus"
                        placeholder="{__("exikane_changes.login_popup_email_placeholder")}"
                    />
                </div>

                <div class="ty-control-group exikane-login-popup__control-group">
                    <label for="psw_{$id}" class="ty-login__filed-label ty-control-group__label cm-required">{__("password")}</label>
                    <div class="exikane-password-wrap">
                        <input
                            type="password"
                            id="psw_{$id}"
                            name="password"
                            size="30"
                            value="{$config.demo_password}"
                            class="ty-login__input"
                            maxlength="32"
                            data-ca-password-toggle-field="true"
                            placeholder="{__("exikane_changes.login_popup_password_placeholder")}"
                        />
                        <button type="button" class="exikane-password-toggle cm-exikane-password-toggle" aria-label="{__("exikane_changes.show_password")}" aria-pressed="false">
                            <i class="ty-icon-eye-open"></i>
                        </button>
                    </div>
                </div>

                {include file="common/image_verification.tpl" option="login" align="left"}

                {hook name="index:login_buttons"}
                    <div class="exikane-login-popup__buttons">
                        <div class="ty-login__remember-me exikane-login-popup__remember-me">
                            <label for="remember_me_{$id}" class="ty-login__remember-me-label">
                                <input type="hidden" name="remember_me" value="N" />
                                <input class="checkbox" type="checkbox" name="remember_me" id="remember_me_{$id}" value="Y" />{__("remember_me")}
                            </label>
                        </div>

                        <button class="ty-btn ty-btn__secondary exikane-login-popup__submit" type="submit" name="dispatch[auth.login]">
                            <bdi>{__("sign_in")}</bdi>
                        </button>

                        <a class="ty-btn ty-btn__secondary exikane-login-popup__register" href="{"profiles.add"|fn_url}" rel="nofollow">
                            <bdi>{__("exikane_changes.create_profile")}</bdi>
                        </a>

                        <div class="ty-login-reglink ty-center exikane-login-popup__forgot">
                            <a class="ty-login-reglink__a" href="{"auth.recover_password"|fn_url}" tabindex="5">{__("forgot_password_question")}</a>
                        </div>
                    </div>
                {/hook}
            </div>
        {else}
            <div class="ty-control-group">
                <label for="login_{$id}" class="ty-login__filed-label ty-control-group__label cm-required cm-trim cm-email">{__("email")}</label>
                <input type="text" id="login_{$id}" name="user_login" size="30" value="{if $stored_user_login}{$stored_user_login}{else}{$config.demo_username}{/if}" class="ty-login__input cm-focus" />
            </div>

            <div class="ty-control-group ty-password-forgot">
                <label for="psw_{$id}" class="ty-login__filed-label ty-control-group__label ty-password-forgot__label cm-required">{__("password")}</label><a href="{"auth.recover_password"|fn_url}" class="ty-password-forgot__a" tabindex="5">{__("forgot_password_question")}</a>
                <div class="exikane-password-wrap">
                    <input type="password" id="psw_{$id}" name="password" size="30" value="{$config.demo_password}" class="ty-login__input" maxlength="32" data-ca-password-toggle-field="true" />
                    <button type="button" class="exikane-password-toggle cm-exikane-password-toggle" aria-label="{__("exikane_changes.show_password")}" aria-pressed="false">
                        <i class="ty-icon-eye-open"></i>
                    </button>
                </div>
            </div>

            {include file="common/image_verification.tpl" option="login" align="left"}

            {if $style == "checkout"}
                </div>
            {/if}

            {hook name="index:login_buttons"}
                <div class="buttons-container clearfix">
                    <div class="ty-login__remember-me">
                        <label for="remember_me_{$id}" class="ty-login__remember-me-label"><input type="hidden" name="remember_me" value="N" /><input class="checkbox" type="checkbox" name="remember_me" id="remember_me_{$id}" value="Y" />{__("remember_me")}</label>
                    </div>
                    {include file="buttons/login.tpl" but_name="dispatch[auth.login]" but_role="submit"}
                </div>
            {/hook}
        {/if}
    </form>
{/capture}

{if $style == "popup"}
    <div id="{$id}_login_popup_form_container">
        {$smarty.capture.login nofilter}
    <!--{$id}_login_popup_form_container--></div>
{else}
    <div class="ty-login">
        {$smarty.capture.login nofilter}
    </div>

    {capture name="mainbox_title"}{__("sign_in")}{/capture}
{/if}
{/ab__hide_content}
