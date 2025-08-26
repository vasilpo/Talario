{if $provider_settings && $settings.abt__ut2.products.addon_social_buttons.view[$settings.ab__device] === "YesNo::YES"|enum}
<div class="ut2-pb__share">
    <a href="javascript:void(0)" rel="nofollow" role="button" id="sw_dropdown_sb" class="ut2-share-buttons-link cm-combination label"><i class="ut2-icon-share"></i>
        <span>{__("abt__ut2.addon_social_buttons.share")}</span>
    </a>
    <span id="dropdown_sb" class="cm-popup-box ty-dropdown-box__content caret hidden cm-smart-position-h">
        {foreach from=$provider_settings item="provider_data"}
            {if $provider_data && $provider_data.template && $provider_data.data}
                {include file="addons/social_buttons/providers/`$provider_data.template`"}
            {/if}
        {/foreach}
    </span>
</div>
{/if}
