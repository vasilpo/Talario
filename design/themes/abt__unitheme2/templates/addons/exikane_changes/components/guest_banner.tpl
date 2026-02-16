{assign var="banner_class" value=$banner_class|default:""}
{assign var="banner_action_style" value=$banner_action_style|default:"button"}

<a class="exikane-guest-banner{if $banner_class} {$banner_class}{/if}" href="{"auth.login_form"|fn_url}">
    <div class="exikane-guest-banner__content">
        {if $banner_title}
            <div class="exikane-guest-banner__title">
                <div class="exikane-guest-banner__title-image">
                    <img width="80px" src="{$images_dir}/addons/exikane_changes/gift-415536.png" />
                </div>
                <div class="exikane-guest-banner__title-text">{$banner_title}</div>
            </div>
        {/if}
        {if $banner_text}
            <div class="exikane-guest-banner__text">{$banner_text}</div>
        {/if}
    </div>
    {if $banner_button_text}
        <div class="exikane-guest-banner__action{if $banner_action_style === "link"} exikane-guest-banner__action--link{/if}">
            <span class="exikane-guest-banner__button{if $banner_action_style === "link"} exikane-guest-banner__button--link{/if}">{$banner_button_text}</span>
        </div>
    {/if}
</a>
