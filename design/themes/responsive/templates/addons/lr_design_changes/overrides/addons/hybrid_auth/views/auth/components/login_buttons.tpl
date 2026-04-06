{hook name="hybrid_auth:login_buttons"}
    {if !isset($redirect_url)}
        {$redirect_url = $config.current_url}
    {/if}
    {if $is_registration|default:false}
        {$hybrid_auth_login_title = __("hybrid_auth.social_login")}
    {else}
        {$hybrid_auth_login_title = __("lr_design_changes.hybrid_auth_social_login_sign_in")}
    {/if}
    {$hybrid_auth_login_title}:
    <p class="ty-text-center">{$smarty.capture.hybrid_auth nofilter}
        {strip}
            <input type="hidden" name="redirect_url" value="{$redirect_url}" />
            {foreach $providers_list as $provider_data}
                {if $provider_data.status === "ObjectStatuses::ACTIVE"|enum}
                    <a class="cm-login-provider ty-hybrid-auth__icon" data-idp="{$provider_data.provider_id}" data-provider="{$provider_data.provider}">
                        <img src="{$provider_data.icon|replace:'/flat_24x24/':'/flat_64x64/'|replace:'/flat_32x32/':'/flat_64x64/'}"
                             title="{if $provider_data.display_name && $provider_data.display_name|strtolower !== $provider_data.provider}{$provider_data.display_name|strtolower}{else}{$provider_data.provider}{/if}"
                             alt="{if $provider_data.display_name && $provider_data.display_name|strtolower !== $provider_data.provider}{$provider_data.display_name|strtolower}{else}{$provider_data.provider}{/if}"
                        />
                    </a>
                {/if}
            {/foreach}
        {/strip}
    </p>
{/hook}
