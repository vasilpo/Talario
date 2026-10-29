{if $config.current_path|default:"" == "/dev_copy" && $runtime.controller|default:"" == "index" && $runtime.mode|default:"" == "index"}
<section id="talario-ux01" class="talario-ux01" data-ux01-server-shell="pending">
    <header class="header"><div class="header-inner wrap">
        <details class="talario-ux01__menu menu">
            <summary class="iconbutton" aria-label="Меню"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"/></svg></summary>
            <nav class="talario-ux01__menu-panel" aria-label="Меню Таларио"><a href="/dev_copy/o-nas/">О нас</a><a href="/dev_copy/blog/">Блог</a><a href="/dev_copy/stat-partnerom/">Стать партнёром</a></nav>
        </details>
        <a class="brand" href="/dev_copy/" aria-label="Таларио — главная"><img class="logo" src="/dev_copy/design/themes/abt__unitheme2/media/images/talario-ux01-logo.png" alt="Таларио" width="140" height="32"></a>
        <span class="header-city">Красногорск</span>
        <nav class="header-links" aria-label="Меню Таларио"><a href="/dev_copy/o-nas/">О нас</a><a href="/dev_copy/blog/">Блог</a><a href="/dev_copy/stat-partnerom/">Стать партнёром</a></nav>
        <div class="header-actions">
            <a class="iconbutton user-link" href="/dev_copy/{if $auth.user_id}profiles-update/{else}login/{/if}" aria-label="{if $auth.user_id}Личный кабинет{else}Войти{/if}">
                {if $auth.user_id}<span class="header-user-name">{$user_info.firstname|default:"Аккаунт"|escape}</span>{else}
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                {/if}
            </a>
            <a class="iconbutton basket" href="/dev_copy/cart/" aria-label="Корзина"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 11-1 9m5-9-4-7M2 11h20m-18.5 0 1.6 7.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.7-7.4M4.5 15.5h15M5 11l4-7m0 7 1 9"/></svg></a>
        </div>
    </div></header>
    <main class="talario-ux01__main">
        <div class="intro wrap"><div class="intro-copy">
            <h1>Занятия для детей<br class="mobile-break"> <span class="city-accent"><span class="city-accent-rule" aria-hidden="true"></span><span class="city-accent-text">в Красногорске</span></span></h1>
            <p class="subtitle">Найди своё — рядом с домом.</p>
        </div><aside class="desktop-bonus"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7v14M20 11v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8M7.5 7a1 1 0 0 1 0-5A4.8 8 0 0 1 12 7a4.8 8 0 0 1 4.5-5 1 1 0 0 1 0 5"/><rect height="4" rx="1" width="18" x="3" y="7"/></svg><div><strong>500 баллов за регистрацию</strong><p>1 балл = 1 ₽</p></div></aside></div>
        <p class="ux01-loading wrap" role="status">Загружаем занятия…</p>
    </main>
</section>
{/if}
