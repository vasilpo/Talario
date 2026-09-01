{style src="addons/talario_vendor_cabinet/styles.less"}
{if $runtime.company_id && ($runtime.controller == "ec_table_booking_system" && $runtime.mode == "booked_orders")}
    {include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
{/if}


{if $runtime.company_id}
{literal}
<style>
/* Тёплая оболочка кабинета партнёра. Подключается напрямую, минуя кеш LESS. */
html body,
html body .main-wrap,
html body .admin-content,
html body .admin-content-wrap,
html body #main_column {
    background: #ffffff !important;
}
html body #top_bar,
html body .top-bar {
    display: none !important;
}
html body .admin-content-wrap {
    min-height: 100vh !important;
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
/* Жёлтый остаётся цветом действия и текущего шага, не цветом фона. */
html body .talario-wizard__step.is-active {
    color: #614a0f !important;
    background: #fff4d8 !important;
}
html body .talario-wizard__step.is-active > span {
    border-color: #e5ae18 !important;
    background: #e5ae18 !important;
}
html body .talario-field input:focus,
html body .talario-field select:focus,
html body .talario-field textarea:focus {
    border-color: #dfb442 !important;
    box-shadow: 0 0 0 3px rgba(229,174,24,.16) !important;
}
</style>
{/literal}
{/if}


{if $runtime.company_id}
{literal}
<style>
html body .cs-main-menu__logo-wrapper,
html body .cs-main-menu__logo-wrapper a,
html body .cs-main-menu__logo-wrapper .logo-container {
    background: #fffaf0 !important;
}
html body .cs-main-menu__logo-wrapper img {
    mix-blend-mode: multiply;
}
</style>
{/literal}
{/if}


{if $runtime.company_id}
{literal}
<style>
/* Единый цвет всей левой панели и подложки логотипа. */
html body .cs-main-menu,
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu .main-menu,
html body .cs-main-menu__logo-wrapper,
html body .cs-main-menu__logo-wrapper a,
html body .cs-main-menu__logo-wrapper .logo-container {
    background: #fffaf0 !important;
}
</style>
{/literal}
{/if}


{if $runtime.company_id}
{literal}
<style>
/* У логотипа и панели один фон, включая внутреннюю обёртку стандартного меню. */
html body .cs-main-menu__logo-wrapper,
html body .cs-main-menu__logo-wrapper .logo-menu__btn,
html body .cs-main-menu__logo-wrapper .logo-menu__btn-inner,
html body .cs-main-menu__logo-wrapper .logo-menu__logo,
html body .cs-main-menu__logo-wrapper .logo-menu__logo-short {
    background-color: #fffaf0 !important;
}
html body .cs-main-menu__logo-wrapper .logo-menu__logo,
html body .cs-main-menu__logo-wrapper .logo-menu__logo-short {
    mix-blend-mode: multiply !important;
}
</style>
{/literal}
{/if}


{if $runtime.company_id}
{literal}
<style>
/* Логотип имеет белую подложку — вся панель приведена к тому же чистому белому. */
html body .cs-main-menu,
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu .main-menu,
html body .cs-main-menu__logo-wrapper,
html body .cs-main-menu__logo-wrapper .logo-menu__btn,
html body .cs-main-menu__logo-wrapper .logo-menu__btn-inner {
    background: #ffffff !important;
}
html body .cs-main-menu__logo-wrapper img {
    mix-blend-mode: normal !important;
}
</style>
{/literal}
{/if}
