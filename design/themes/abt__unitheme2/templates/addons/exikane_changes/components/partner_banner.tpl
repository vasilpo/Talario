<div class="exikane-partner-banner{if $banner_class} {$banner_class}{/if}">
    <div class="exikane-partner-banner__content">
        <div class="exikane-partner-banner__title">
            <span class="exikane-partner-banner__icon">
                {include_ext file="common/icon.tpl" class="ty-icon-lock"}
            </span>
            <span>{__("exikane_changes.partner_banner_title")}</span>
        </div>
        <div class="exikane-partner-banner__text">
            {__("exikane_changes.partner_banner_text")}
        </div>
    </div>
    <div class="exikane-partner-banner__footer">
        <div class="exikane-partner-banner__login">
            {__("exikane_changes.partner_banner_have_account")}
            <a href="{"auth.login_form"|fn_url}">{__("exikane_changes.partner_banner_login")}</a>
        </div>
        <a class="ty-btn ty-btn__primary exikane-partner-banner__button" href="{"profiles.add"|fn_url}">
            {__("exikane_changes.partner_banner_button")}
        </a>
    </div>
</div>
