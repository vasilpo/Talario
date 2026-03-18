{if $display_button_block}
    {$social_buttons_class = $social_buttons_class|default:""}

    {include file="addons/social_buttons/components/product_detail_bottom_pre.tpl"}

    <ul class="ty-social-buttons {$social_buttons_class}">
        {foreach from=$provider_settings item="provider_data"}
            {if $provider_data && $provider_data.template && $provider_data.data}
                <li class="ty-social-buttons__inline">{include file="addons/social_buttons/providers/`$provider_data.template`"}</li>
            {/if}
        {/foreach}
    </ul>

    {include file="addons/social_buttons/components/product_detail_bottom_post.tpl"}
{/if}
