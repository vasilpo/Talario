{if $pwa_is_valid}{strip}
    <link rel="manifest" href="{$config.current_location}/manifest.json?v={$manifest_version}" />
{/strip}{/if}