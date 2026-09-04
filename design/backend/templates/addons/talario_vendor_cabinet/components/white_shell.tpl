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
    padding-top: var(--top-bar-height) !important;
}
html body .admin-content-wrap.admin-content-wrap--scroll-header {
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

/*
 * Шапка кабинета: поиск остаётся в геометрическом центре экрана,
 * независимо от длины названия партнёра справа.
 */
html body .top-bar__inner {
    grid-template-columns: minmax(0, 1fr) 382px minmax(0, 1fr) !important;
    gap: 0 !important;
    padding-right: 16px !important;
    padding-left: 16px !important;
}
html body .top-bar__search {
    width: 382px !important;
    justify-self: center !important;
}
html body .top-bar__search .search {
    width: 100% !important;
    margin: 0 !important;
}
html body .top-bar__search .search__group {
    display: flex !important;
    box-sizing: border-box !important;
    width: 100% !important;
    height: 28px !important;
    overflow: hidden !important;
    border: 1px solid #d9e0e8 !important;
    border-radius: 4px !important;
    background: #fffdf9 !important;
}
html body .top-bar__search input[type="text"].search__input {
    min-width: 0 !important;
    height: 26px !important;
    padding: 4px 8px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: #34312c !important;
    font-size: 12px !important;
    line-height: 18px !important;
}
html body .top-bar__search .search__input::placeholder {
    color: #8b887f !important;
}
html body .top-bar__search .search__button {
    display: inline-flex !important;
    flex: 0 0 34px !important;
    align-items: center !important;
    justify-content: center !important;
    height: 26px !important;
    min-height: 26px !important;
    min-width: 34px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
html body .top-bar__search .search__button:before {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
}
html body .talario-add-class-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-sizing: border-box !important;
    min-height: 38px !important;
    padding: 9px 14px !important;
    line-height: 18px !important;
    white-space: nowrap !important;
    text-align: center !important;
}

/* Desktop layout for «Центр»: the approved compact form and full-width panels. */
html body .talario-center-page,
html body .talario-classes-page {
    max-width: 1280px !important;
    color: #302f2b !important;
}
html body .talario-center-page .talario-todo,
html body .talario-classes-page .talario-class-card {
    box-sizing: border-box !important;
    border: 1px solid #e4e1da !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    box-shadow: 0 2px 5px rgba(61, 52, 38, .035) !important;
}
html body .talario-center-page .talario-todo {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 22px 24px !important;
}
html body .talario-center-page .talario-todo + .talario-todo {
    margin-top: 16px !important;
}
html body .talario-section-heading,
html body .talario-center-page .talario-dashboard__header {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 20px !important;
    margin: 0 0 20px !important;
}
html body .talario-section-heading h2,
html body .talario-center-page .talario-dashboard__header h2 {
    margin: 0 !important;
    color: #302f2b !important;
    font-size: 20px !important;
    font-weight: 700 !important;
    letter-spacing: -.01em !important;
    line-height: 1.25 !important;
}
html body .talario-section-heading .muted,
html body .talario-center-page .talario-dashboard__header .muted {
    margin: 5px 0 0 !important;
    color: #7b7972 !important;
    font-size: 13px !important;
    line-height: 1.45 !important;
}
html body .talario-center-form {
    max-width: 760px !important;
}
html body .talario-center-form .control-group {
    display: grid !important;
    grid-template-columns: 160px minmax(0, 570px) !important;
    align-items: start !important;
    gap: 0 14px !important;
    margin: 0 0 16px !important;
}
html body .talario-center-form .control-label {
    float: none !important;
    width: auto !important;
    margin: 0 !important;
    padding: 10px 0 0 !important;
    color: #474640 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 18px !important;
    text-align: right !important;
}
html body .talario-center-form .controls {
    min-width: 0 !important;
    margin-left: 0 !important;
}
html body .talario-center-form input[type="text"],
html body .talario-center-form textarea {
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 570px !important;
    min-height: 38px !important;
    margin: 0 !important;
    padding: 8px 11px !important;
    border: 1px solid #d9d7d1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    box-shadow: none !important;
    color: #383733 !important;
    font-size: 13px !important;
    line-height: 20px !important;
}
html body .talario-center-form textarea {
    min-height: 88px !important;
    resize: vertical !important;
}
html body .talario-center-form .description {
    margin: 6px 0 0 !important;
    color: #8a8780 !important;
    font-size: 12px !important;
    line-height: 1.35 !important;
}
html body .talario-center-form .buttons-container {
    margin: 4px 0 0 174px !important;
}
html body .talario-center-page .btn-primary,
html body .talario-add-class-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 38px !important;
    padding: 9px 14px !important;
    border: 0 !important;
    border-radius: 7px !important;
    background: #f3bf25 !important;
    color: #453609 !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    line-height: 18px !important;
    text-decoration: none !important;
    text-shadow: none !important;
    white-space: nowrap !important;
    box-shadow: 0 3px 8px rgba(177, 128, 12, .16) !important;
}
html body .talario-center-page .table {
    margin: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
}
html body .talario-center-page .table th {
    padding: 0 8px 10px !important;
    border-bottom: 1px solid #e8e5de !important;
    color: #88847b !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    text-transform: none !important;
}
html body .talario-center-page .table td {
    padding: 13px 8px !important;
    border-bottom: 0 !important;
    color: #4c4a45 !important;
    font-size: 13px !important;
    vertical-align: middle !important;
}
html body .talario-center-page .table .btn {
    min-height: 32px !important;
    padding: 6px 11px !important;
    border: 1px solid #d9d7d1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #4b4944 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 18px !important;
}

/* Desktop layout for «Занятия»: filters, primary action and activity cards. */
html body .talario-classes-toolbar {
    align-items: center !important;
    margin: 0 0 20px !important;
}
html body .talario-classes-page .talario-filters {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 7px !important;
}
html body .talario-classes-page .talario-filters .btn {
    display: inline-flex !important;
    align-items: center !important;
    min-height: 34px !important;
    padding: 7px 12px !important;
    border: 1px solid #ddd9d1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #55534e !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 18px !important;
    text-decoration: none !important;
    text-shadow: none !important;
    box-shadow: none !important;
}
html body .talario-classes-page .talario-filters .btn-primary {
    border-color: #f0bd2b !important;
    background: #fff1cc !important;
    color: #4c3b08 !important;
}
html body .talario-classes-page .talario-class-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
}
html body .talario-classes-page .talario-class-card {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    overflow: hidden !important;
    padding: 0 !important;
}
html body .talario-classes-page .talario-class-card__image {
    height: 184px !important;
    overflow: hidden !important;
    background: #f3f1ec !important;
}
html body .talario-classes-page .talario-class-card__image img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
html body .talario-classes-page .talario-class-card__body {
    display: flex !important;
    flex: 1 1 auto !important;
    flex-direction: column !important;
    padding: 16px !important;
}
html body .talario-classes-page .talario-class-card__heading h3 {
    min-height: 42px !important;
    margin: 0 !important;
    color: #35342f !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    line-height: 1.4 !important;
}
html body .talario-classes-page .talario-class-card__meta {
    margin: 15px 0 16px !important;
    color: #3b3934 !important;
    font-size: 13px !important;
}
html body .talario-classes-page .talario-class-card__meta .label {
    padding: 3px 7px !important;
    border-radius: 99px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 16px !important;
}
html body .talario-classes-page .talario-class-card__actions {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 8px !important;
    margin-top: auto !important;
}
html body .talario-classes-page .talario-class-card__actions .btn {
    box-sizing: border-box !important;
    min-width: 0 !important;
    min-height: 34px !important;
    margin: 0 !important;
    padding: 7px 10px !important;
    border: 1px solid #d9d7d1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #4b4944 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 18px !important;
    text-decoration: none !important;
    text-shadow: none !important;
}
@media (max-width: 767px) {
    html body .top-bar__inner {
        grid-template-columns: minmax(0, 1fr) minmax(180px, 38vw) minmax(0, 1fr) !important;
    }
    html body .top-bar__search {
        width: 100% !important;
    }
    html body .talario-center-form .control-group {
        grid-template-columns: 1fr !important;
    }
    html body .talario-center-form .control-label {
        padding-top: 0 !important;
        text-align: left !important;
    }
    html body .talario-center-form .buttons-container {
        margin-left: 0 !important;
    }
    html body .talario-classes-page .talario-class-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
{/literal}
