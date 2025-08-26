{if $settings.ab__device !== "mobile"}
    {if $provider_settings && $settings.abt__ut2.products.addon_social_buttons.view[$settings.ab__device] === "YesNo::YES"|enum}
    	{include file="addons/social_buttons/blocks/components/share_buttons.tpl"}
    {/if}
{/if}