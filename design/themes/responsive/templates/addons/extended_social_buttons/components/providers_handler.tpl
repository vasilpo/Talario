{if $addons.extended_social_buttons.social_settings.$provider === 'Y'}
    <div class="{$provider}_extended_share">
        <a id="{$provider}_extended_share_btn" target="_blank" {if $provider === 'pinterest'}data-pin-custom="true"{/if}><img src="{$images_dir}/addons/extended_social_buttons/{$provider}.svg" /><span class="share-txt hidden-phone">{if $provider === 'twitter'}{__("extended_social_buttons.extended_share_tweet")}{elseif $provider === 'pinterest'}{__("extended_social_buttons.extended_share_pin")}{else}{__("extended_social_buttons.extended_share")}{/if}</span></a>
        {include file="addons/extended_social_buttons/providers/{$provider}.tpl"}
    </div>
{/if}