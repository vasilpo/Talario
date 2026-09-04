{literal}
<style>
/*
 * Единая кремовая оболочка кабинета партнёра.
 * Подключается последней внутри каждой страницы кабинета и заменяет
 * разношёрстные фоны системными переменными CS-Cart.
 */
:root {
    --cs-content-background: #fffdf9;
    --cs-body-background: #f8f2e8;
    --cs-menu-sidebar-color: #f8f2e8;
    --cs-menu-sidebar-bg: none;
    --cs-admin-content-wrap-shadow: none;
    --cs-admin-content-wrap-radius: 0px;
    --cs-form-actions-background: #ffffff;
}

html,
html body,
/* Фон внешней области образует шапку справа от меню. */
html body .main-wrap,
html body .admin-content {
    background: #f8f2e8 !important;
}
/* Рабочая область остаётся почти белой и отделяется от шапки. */
html body .admin-content-wrap,
html body .admin-content-wrapper-outer,
html body .admin-content-wrapper,
html body .page-content,
html body .content-wrap,
html body #main_column,
html body .mainbox-container,
html body .mainbox-body,
html body .ty-mainbox-container,
html body .talario-cabinet,
html body .talario-home {
    background: #fffdf9 !important;
}

/* Верхняя панель содержит название партнёра и уведомления. */
html body #top_bar,
html body .top-bar,
html body .top-bar__inner {
    background-color: #f8f2e8 !important;
    background-image: none !important;
    border-bottom: 1px solid #eee5d9 !important;
}
html body #header_navbar {
    background-color: #f8f2e8 !important;
    background-image: none !important;
    border-bottom: 1px solid #eee5d9 !important;
}
html body .admin-content-wrap {
    padding-top: 0 !important;
}

/* Боковая панель, логотип и навигация — единый кремовый фон. */
html body .cs-main-menu {
    background-color: #f8f2e8 !important;
    background-image: none !important;
    border-right: 1px solid #eee5d9 !important;
    box-shadow: none !important;
}
html body .cs-main-menu__header,
html body .cs-main-menu__outer,
html body .cs-main-menu__inner,
html body .cs-main-menu__primary,
html body .cs-main-menu__secondary,
html body .cs-main-menu__bottom-toolbar,
html body .cs-main-menu__logo-wrapper,
html body .cs-main-menu__logo-wrapper .logo-menu__btn,
html body .cs-main-menu__logo-wrapper .logo-menu__btn-inner {
    background-color: transparent !important;
    background-image: none !important;
    border-color: transparent !important;
    box-shadow: none !important;
}

/* Ссылки прозрачны: меню больше не выглядит набором белых полос. */
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

/* Пустые состояния также остаются на белом фоне. */
html body .no-items,
html body #elm_booking .no-items {
    background: #ffffff !important;
}

/* Жёлтый остаётся акцентом действия, а не подложкой страницы. */
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
    box-shadow: 0 0 0 3px rgba(229, 174, 24, .16) !important;
}
</style>
{/literal}
