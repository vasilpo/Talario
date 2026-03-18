{hook name="block_manager:frontend_container"}
    {if $runtime.customization_mode.block_manager && $location_data.is_frontend_editing_allowed}
        {include file="backend:views/block_manager/frontend_render/container.tpl"}
    {else}
        <div class="{if $layout_data.layout_width != "fixed"}container-fluid{else}container{/if}{if $container.position|lower != "header"} {$container.user_class}{/if}">
            {$content nofilter}
        </div>
    {/if}
    {capture name="container_user_class_`$container.position|lower`"}{$container.user_class}{/capture}
{/hook}