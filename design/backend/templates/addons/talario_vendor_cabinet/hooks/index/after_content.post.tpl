{if $runtime.company_id}
{literal}
<style>
/* Финальный слой кабинета: идёт после содержимого страниц и отменяет устаревшие голубые фоны. */
html body,
html body .main-wrap,
html body .admin-content,
html body .admin-content-wrap,
html body .main-content,
html body .content-wrap,
html body #main_column,
html body .mainbox-container,
html body .mainbox-body,
html body .ty-mainbox-container,
html body .talario-cabinet,
html body .talario-home {
    background: #ffffff !important;
}
html body .cs-main-menu,
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu .main-menu {
    background: #faf8f3 !important;
}
html body .cs-main-menu {
    border-right: 1px solid #e8e2d8 !important;
}
html body .cs-main-menu .main-menu-1__link,
html body .cs-main-menu .main-menu-1__toggle,
html body .cs-main-menu .main-menu-2__link,
html body .cs-main-menu .main-menu-3__link {
    color: #3f403f !important;
}
html body .cs-main-menu .main-menu-1__link:hover,
html body .cs-main-menu .main-menu-1__toggle:hover {
    background: #f5f1e8 !important;
}
html body .cs-main-menu .main-menu-1__link--active,
html body .cs-main-menu .main-menu-1__toggle--active,
html body .cs-main-menu .main-menu-2__link--active,
html body .cs-main-menu .main-menu-3__link--active {
    background: #fff1c9 !important;
    color: #4a3a0f !important;
    box-shadow: inset 3px 0 0 #f3bd25 !important;
}
html body .cs-main-menu .main-menu-1__link--active .cs-icon__svg,
html body .cs-main-menu .main-menu-1__toggle--active .cs-icon__svg,
html body .cs-main-menu .main-menu-1__link--active .main-menu-1__icon {
    color: #c28b12 !important;
}
</style>
{/literal}
{/if}
