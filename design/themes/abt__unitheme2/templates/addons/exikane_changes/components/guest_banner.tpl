{assign var="banner_class" value=$banner_class|default:""}
{assign var="banner_action_style" value=$banner_action_style|default:"button"}
{assign var="banner_href" value=$banner_href|default:{"auth.login_form"|fn_url}}

<a class="exikane-guest-banner{if $banner_class} {$banner_class}{/if}" href="{$banner_href}">
    <div class="exikane-guest-banner__main">
        <div class="exikane-guest-banner__image">
            <img src="{$images_dir}/addons/exikane_changes/gift-415536.png" alt="{__("exikane_changes.guest_banner_icon_alt")}" />
        </div>
        <div class="exikane-guest-banner__content">
            {if $banner_title}
                <div class="exikane-guest-banner__title">{$banner_title nofilter}</div>
            {/if}
            {if $banner_text}
                <div class="exikane-guest-banner__text">{$banner_text nofilter}</div>
            {/if}
        </div>
    </div>
    {if $banner_button_text}
        <div class="exikane-guest-banner__action{if $banner_action_style === "link"} exikane-guest-banner__action--link{/if}">
            <span class="exikane-guest-banner__button{if $banner_action_style === "link"} exikane-guest-banner__button--link{/if}">{$banner_button_text}</span>
        </div>
    {/if}
</a>
