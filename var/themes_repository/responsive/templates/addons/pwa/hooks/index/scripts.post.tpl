{if $pwa_is_valid}{strip}
    {$service_worker_scripts[] = 'js/addons/pwa/frontend/sw_pwa.js'}
    {$service_worker_scripts = $service_worker_scripts scope=parent}
{/strip}{/if}