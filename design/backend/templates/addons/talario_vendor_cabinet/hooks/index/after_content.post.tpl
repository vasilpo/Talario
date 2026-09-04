{if $runtime.company_id}
{literal}
<style>
/* Финальный слой кабинета: единая кремовая оболочка поверх системных стилей. */
html {
    --cs-content-background: #fffdf9;
    --cs-body-background: #f8f2e8;
    --cs-menu-sidebar-color: #f8f2e8;
    --cs-menu-sidebar-bg: none;
    --cs-form-actions-background: #ffffff;
}
html body .main-wrap,
html body .admin-content {
    background: #f8f2e8 !important;
}
html body .admin-content-wrap,
html body .content-wrap {
    background: #fffdf9 !important;
}
/* Keep the page heading in one place while the vendor scrolls.
   CS-Cart normally swaps this text into the actions panel on scroll. */
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
html body .cs-main-menu,
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu__bottom-toolbar,
html body .cs-main-menu__logo-wrapper {
    background-color: #f8f2e8 !important;
    background-image: none !important;
}
html body #header_navbar {
    background-color: #f8f2e8 !important;
    border-bottom: 1px solid #eee5d9 !important;
}
html body .cs-main-menu {
    border-right: 1px solid #eee5d9 !important;
    box-shadow: none !important;
}
html body .cs-main-menu .main-menu-1__link,
html body .cs-main-menu .main-menu-1__toggle,
html body .cs-main-menu .main-menu-2__link,
html body .cs-main-menu .main-menu-3__link {
    background-color: transparent !important;
    color: #3f403f !important;
}
html body .cs-main-menu .main-menu-1__link:hover,
html body .cs-main-menu .main-menu-1__toggle:hover {
    background-color: #fff8e8 !important;
}
html body .cs-main-menu .main-menu-1__link--active,
html body .cs-main-menu .main-menu-1__toggle--active,
html body .cs-main-menu .main-menu-2__link--active,
html body .cs-main-menu .main-menu-3__link--active {
    background-color: #fff1c9 !important;
    color: #4a3a0f !important;
    box-shadow: inset 3px 0 0 #f3bd25 !important;
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
