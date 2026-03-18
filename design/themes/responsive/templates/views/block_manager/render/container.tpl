{hook name="block_manager:frontend_container"}
    {if $runtime.customization_mode.block_manager && $location_data.is_frontend_editing_allowed}
        {include file="backend:views/block_manager/frontend_render/container.tpl"}
    {else}
        <div class="{if $layout_data.layout_width != "fixed"}container-fluid {else}container{/if} ty-cs-controller-{$runtime.controller} ty-cs-mode-{$runtime.mode} {if $runtime.action}ty-cs-action-{$runtime.action}{/if} {$container.user_class}">
            {$content nofilter}
        </div>
    {/if}
{/hook}