{if $providers_list}
    {if $auth.user_id}
        {hook name="hybrid_auth:account_update"}
            {include file="common/subheader.tpl" title=__("hybrid_auth.link_provider")}
            <p>{__("hybrid_auth.text_link_provider")}</p>

            <div class="clearfix ty-hybrid-auth__icon-container" id="hybrid_providers">
                {foreach $providers_list as $provider_data}
                    {if in_array($provider_data.provider, $linked_providers)}
                        <div class="ty-hybrid-auth__icon ty-float-left">
                            <a class="cm-unlink-provider ty-hybrid-auth__remove" data-idp="{$provider_data.provider_id}" data-provider="{$provider_data.provider}">{include_ext file="common/icon.tpl" class="ty-icon-cancel-circle"}</a>
                            <img src="{$provider_data.icon}" title="{__("hybrid_auth.linked_provider")}" alt="{if $provider_data.display_name && $provider_data.display_name|strtolower !== $provider_data.provider}{$provider_data.display_name|strtolower}{else}{$provider_data.provider}{/if}"/>
                        </div>
                    {/if}
                {/foreach}
                <div class="ty-hybrid-auth__icon ty-float-left">&nbsp;</div>
                {foreach $providers_list as $provider_data}
                    {if !in_array($provider_data.provider, $linked_providers)}
                        <div class="ty-hybrid-auth__icon ty-float-left">
                            <a class="cm-link-provider ty-link-unlink-provider" data-idp="{$provider_data.provider_id}" data-provider="{$provider_data.provider}">
                                {include_ext file="common/icon.tpl"
                                    class="ty-icon-plus-circle ty-hybrid-auth__add"
                                }
                                <img
                                    src="{$provider_data.icon}"
                                    title="{__("hybrid_auth.not_linked_provider")}"
                                    alt="{if $provider_data.display_name && $provider_data.display_name|strtolower !== $provider_data.provider}{$provider_data.display_name|strtolower}{else}{$provider_data.provider}{/if}"
                                />
                            </a>
                        </div>
                    {/if}
                {/foreach}
                <!--hybrid_providers--></div>
        {/hook}
    {else}
        {include file="addons/hybrid_auth/views/auth/components/login_buttons.tpl"}
    {/if}
{/if}
