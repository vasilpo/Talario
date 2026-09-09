{if $runtime.company_id}
{literal}
<style>
/*
 * Keep only behavior that is specific to CS-Cart's scrolling action panel here.
 * Visual shell colors/menu/header are owned by white_shell.tpl and must not be
 * duplicated in this late hook.
 */
html body #actions_panel {
    position: static !important;
    top: auto !important;
    z-index: auto !important;
}
html body #actions_panel [data-ca-mainbox="navActionsTitle"] {
    visibility: hidden !important;
}
html body [data-ca-mainbox="contentHeadingTitle"] {
    visibility: visible !important;
}
</style>
{/literal}
{/if}

{* The dashboard has its own content controls, so the empty system action bar is not needed. *}
{if $runtime.company_id && $runtime.controller === "talario_dashboard"}
{literal}
<style>
html body #actions_panel {
    display: none !important;
}
</style>
{/literal}
{/if}
