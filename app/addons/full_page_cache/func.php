<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

use Tygh\Addons\FullPageCache\Addon;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Providers\FullPageCacheProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

Tygh::$app['class_loader']->add('', __DIR__);
Tygh::$app->register(new FullPageCacheProvider());

/**
 * Checks session is active
 *
 * @return bool
 */
function fn_full_page_cache_is_session_active()
{
    static $active;

    if ($active !== null) {
        return $active;
    }

    /** @var \Tygh\Web\Session $session */
    $session = Tygh::$app['session'];

    return $active = $session->isStarted();
}

/**
 * Checks current dispatch is cacheable
 *
 * @return bool
 */
function fn_full_page_cache_is_current_dispatch_cacheable()
{
    static $cachable;

    if ($cachable !== null) {
        return $cachable;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $auth = Tygh::$app['session']['auth'];

    return $cachable = (
        AREA === SiteArea::STOREFRONT
        && $_SERVER['REQUEST_METHOD'] === 'GET'
        && empty($auth['user_id'])
        && $addon->getCookie('disable_cache') === null
        && !$addon->isEsiRequest()
        && $addon->isDispatchCacheable(
            Registry::get('runtime.controller'),
            Registry::get('runtime.mode'),
            Registry::get('runtime.action')
        )
    );
}

/**
 * Checks current action is cacheable
 *
 * @return bool
 */
function fn_full_page_cache_is_current_action_cacheable()
{
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    return !$addon->isCookieSend()
        && !(defined('AJAX_REQUEST') && fn_full_page_cache_is_session_active())
        && !Registry::get('runtime.full_page_cache.notification_exists');
}


/**
 * Checks current request is cacheable
 *
 * @return bool
 */
function fn_full_page_cache_is_current_request_cacheable()
{
    return fn_full_page_cache_is_current_dispatch_cacheable() && fn_full_page_cache_is_current_action_cacheable();
}

/**
 * Sets cookie that prevent use caching for once time.
 *
 * @return void
 */
function fn_full_page_cache_disable_cache_once_by_cookie()
{
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $addon->setCookie('disable_cache', 'O', time() + COOKIE_ALIVE_TIME);
}

/**
 * Sets cookie that prevent use caching for all time.
 *
 * @return void
 */
function fn_full_page_cache_disable_cache_by_cookie()
{
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $addon->setCookie('disable_cache', 'Y', time() + COOKIE_ALIVE_TIME);
}

/**
 * Removes cookie that prevent use caching for all time.
 *
 * @return void
 */
function fn_full_page_cache_enable_cache_by_cookie()
{
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $addon->removeCookie('disable_cache');
}

/**
 * Hook is used to install the unmanaged addon together with main addon.
 *
 * @return void
 */
function fn_full_page_cache_install()
{
    $root_dir = Registry::get('config.dir.root');
    if (is_writable($root_dir)) {
        // Check if symlink already exists and correct, if not - create it
        if (is_link($root_dir . '/esi.php')) {
            $current_symlink = readlink($root_dir . '/esi.php');

            if ($current_symlink !== $root_dir . '/app/addons/full_page_cache/esi.php') {
                unlink($root_dir . '/esi.php');
                symlink('app/addons/full_page_cache/esi.php', $root_dir . '/esi.php');
            }
        } else {
            symlink('app/addons/full_page_cache/esi.php', $root_dir . '/esi.php');
        }
    } else {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('full_page_cache.warning_root_dir_not_writable'));
    }

    fn_set_notification(
        NotificationSeverity::WARNING,
        __('warning'),
        __(
            'full_page_cache.notice_configure_addon',
            ['[url]' => fn_url('addons.update&addon=full_page_cache&selected_section=settings')]
        )
    );

    fn_install_addon('full_page_cache_unmanaged', false, false, true);
}

/**
 * Hook is used to install the unmanaged addon together with main addon.
 *
 * @return void
 */
function fn_full_page_cache_uninstall()
{
    $root_dir = Registry::get('config.dir.root');
    if (is_link($root_dir . '/esi.php') && is_writable($root_dir)) {
        unlink($root_dir . '/esi.php');
    }

    fn_uninstall_addon('full_page_cache_unmanaged', false, true);
}

/**
 * The "render_block_pre" hook handler.
 *
 * Actions performed:
 *  - This hook handler determines whether ESI-rendering should be enabled for the block being currently rendered.
 *
 * @param array $block        Block data
 * @param array $block_schema Block schema
 * @param array $params       Rendering parameters
 *
 * @see \Tygh\BlockManager\RenderManager::renderBlockContent
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_render_block_pre(array $block, array $block_schema, array &$params)
{
    if (AREA === 'A' || defined('API')) {
        $params['esi_enabled'] = false;
    } elseif (!isset($params['esi_enabled'])) {
        $params['esi_enabled'] = $block_schema['session_dependent']
            && fn_full_page_cache_is_session_active()
            && fn_full_page_cache_is_current_request_cacheable()
            && Registry::get('runtime.controller_status') === CONTROLLER_STATUS_OK;
    }

    if ($params['esi_enabled']) {
        $params['use_cache'] = false;
    }
}

/**
 * The "render_block_post" hook handler.
 *
 * Actions performed:
 *  - If ESI is enabled, this hook handle wrap the block content to esi XML tags.
 *
 * @param array       $block                 Block data
 * @param array       $block_schema          Block schema
 * @param string|null $block_content         Block content. You may modify already rendered content by changing this variable contents.
 * @param bool        $load_block_from_cache Whether block content was found at cache and was loaded out of there
 * @param bool        $display_this_block    Whether block should be displayed
 * @param array       $params                Rendering parameters
 *
 * @see \Tygh\BlockManager\RenderManager::renderBlockContent
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_render_block_post(
    array $block,
    array $block_schema,
    &$block_content,
    $load_block_from_cache,
    $display_this_block,
    array $params
) {
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    if ($params['esi_enabled'] && $display_this_block) {
        $block_content = $addon->renderESIForBlock(
            $block,
            $block_content ?? '',
            CART_LANGUAGE,
            Registry::get('config.http_location'),
            $_SERVER['REQUEST_URI'],
            (defined('DEVELOPMENT') && DEVELOPMENT)
        );
    }
}

/**
 * The "dispatch_before_send_response" hook handler.
 *
 * Actions performed:
 *  - If dispatch is cacheable, this hook handler sets HTTP headers for caching by cache server.
 *
 * @param string $status     Controller response status
 * @param string $area       Currently running application area
 * @param string $controller Executed controller
 * @param string $mode       Executed mode
 * @param string $action     Executed action
 *
 * @see \Tygh\Registry::save
 *
 * @return void
 */
function fn_full_page_cache_dispatch_before_send_response($status, $area, $controller, $mode, $action)
{
    if (
        $status !== CONTROLLER_STATUS_OK
        || $area !== SiteArea::STOREFRONT
        || !fn_full_page_cache_is_current_request_cacheable()
    ) {
        return;
    }

    /** @var \Tygh\Addons\FullPageCache\Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    $addon->registerPageCacheTags([Addon::GLOBAL_TAG_CACHE]);
    $addon->registerPageCacheTags($addon->getGlobalCacheTags());

    foreach (Registry::getCachedKeys() as $cached_key) {
        if (isset($cached_key['condition']) && is_array($cached_key['condition'])) {
            $addon->registerPageCacheTags($cached_key['condition']);
        }
    }

    $addon->registerPageCahceTTL($controller, $mode, $action);

    if (fn_full_page_cache_is_session_active()) {
        $addon->setIsAllowEsi(true);
    }

    foreach ($addon->getPageHeaders() as $header) {
        header($header);
    }
}

/**
 * The "registry_save_pre" hook handler.
 *
 * Actions performed:
 *  - Invalidates page cache records.
 *
 * @param array $changed_tables Changed tables data
 *
 * @see \Tygh\Registry::save
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_registry_save_pre(array $changed_tables)
{
    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $addon->invalidateByTags(array_keys($changed_tables));
}

/**
 * The "get_route" hook handler.
 *
 * IMPORTANT! This handler must run before the handler of the SEO add-on,
 * because it might initialize `sl` params.
 *
 * Actions performed:
 *  - If the cookie contains the `sl` parameter and url is not allowed, this hook handler sets `sl` to request.
 *
 * @param array  $req            Request parameters
 * @param array  $result         Request result
 * @param string $area           Site's area
 * @param bool   $is_allowed_url Flag that determines if url is supported
 *
 * @see fn_get_route
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_get_route(array &$req, array $result, $area, $is_allowed_url)
{
    // 1. For storefront only
    // 2. Only if the language is not explicitly passed through the request
    // 3. Only if the current URL is an SEO URL, for example:
    //       /office-supplies/desk-accessories/ - SEO url
    //       /index.php?dispatch=product_features.view_all&filter_id=10 - not SEO url

    if ($area !== SiteArea::STOREFRONT || isset($req['sl']) || $is_allowed_url) {
        return;
    }

    $show_secondary_language_in_uri = YesNo::toBool(Registry::get('addons.seo.seo_language'));

    if ($show_secondary_language_in_uri) {
        return;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    if ($addon->hasCookie('sl')) {
        $req['sl'] = $addon->getCookie('sl');
    }
}

/**
 * The "get_route_runtime" hook handler.
 *
 * Actions performed:
 *  - If the cookie contains the `sl` parameter, this hook handler sets `sl` to request.
 *
 * @param array  $req  Request
 * @param string $area Site area
 *
 * @see fn_get_route
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_get_route_runtime(array &$req, $area)
{
    // Performs processing after the execution of the SEO add-on hooks
    // 1. For storefront only
    // 2. Only if the language is not explicitly passed through the request

    if ($area !== SiteArea::STOREFRONT || isset($req['sl'])) {
        return;
    }

    $show_secondary_language_in_uri = YesNo::toBool(Registry::get('addons.seo.seo_language'));

    // In the case of switching the language on the home page (index.index), request do not fall under the fn_full_page_cache_get_route rules
    if ($show_secondary_language_in_uri && isset($req['dispatch']) && $req['dispatch'] === 'index.index') {
        return;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    if ($addon->hasCookie('sl')) {
        $req['sl'] = $addon->getCookie('sl');
    }
}

/**
 * The "init_currency_pre" hook handler.
 *
 * Actions performed:
 *  - If the cookie contains the `currency` parameter, this hook handler sets `currency` to request.
 *
 * @param array  $params Parameters
 * @param string $area   Site area
 *
 * @see fn_init_currency
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_init_currency_pre(array &$params, $area)
{
    if ($area !== SiteArea::STOREFRONT || isset($params['currency'])) {
        return;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    if ($addon->hasCookie('currency')) {
        $params['currency'] = $addon->getCookie('currency');
    }
}

/**
 * The "init_user" hook handler.
 *
 * Actions performed:
 *  - If selected language is different than default language, this hook handler sets `sl` to cookie.
 *  - If selected currency is different than default currency, this hook handler sets `currency` to cookie.
 *
 * @param array $auth Auth data
 *
 * @see fn_init_user
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_full_page_cache_user_init(array $auth)
{
    if (AREA !== SiteArea::STOREFRONT) {
        return;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $default_language = Registry::get('settings.Appearance.frontend_default_language');

    if (!empty($auth['user_id'])) {
        fn_full_page_cache_disable_cache_by_cookie();
        Registry::set('config.tweaks.anti_csrf', Registry::get('config.tweaks.anti_csrf_original'));
    } elseif ($addon->getCookie('disable_cache') === 'O' && !defined('AJAX_REQUEST')) {
        fn_full_page_cache_enable_cache_by_cookie();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !defined('AJAX_REQUEST')) {
        fn_full_page_cache_disable_cache_once_by_cookie();
    }

    if ($addon->hasCookie('sl') && $addon->getCookie('sl') === CART_LANGUAGE) {
        unset($_REQUEST['sl']);
    }

    if (
        CART_LANGUAGE !== $default_language
        && CART_LANGUAGE !== $addon->getCookie('sl')
    ) {
        $addon->setCookie('sl', CART_LANGUAGE);
    } elseif (CART_LANGUAGE === $default_language && $addon->hasCookie('sl')) {
        $addon->removeCookie('sl');
    }

    if (
        CART_SECONDARY_CURRENCY !== CART_PRIMARY_CURRENCY
        && CART_SECONDARY_CURRENCY !== $addon->getCookie('currency')
    ) {
        $addon->setCookie('currency', CART_SECONDARY_CURRENCY);
    } elseif (CART_SECONDARY_CURRENCY === CART_PRIMARY_CURRENCY && $addon->hasCookie('currency')) {
        $addon->removeCookie('currency');
    }
}

/**
 * The "clear_cache_post" hook handler.
 *
 * Actions performed:
 *  - Invalidates all cache records
 *
 * @param string $type Type
 *
 * @see fn_clear_cache
 *
 * @return void
 */
function fn_full_page_cache_clear_cache_post($type)
{
    if (!in_array($type, ['registry', 'all', 'assets'])) {
        return;
    }

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    $addon->invalidateByTags([Addon::GLOBAL_TAG_CACHE]);
}

/**
 * The "db_query_executed" hook handler.
 *
 * Actions performed:
 *  - Retrieves table names from SELECT query for usage as cach tags
 *
 * @param string             $query  The sql query
 * @param object|string|null $result An instance of the result returned from the database
 *
 * @see \Tygh\Database\Connection::query
 *
 * @return void
 */
function fn_full_page_cache_db_query_executed($query, $result)
{
    if (
        AREA !== SiteArea::STOREFRONT
        || !is_object($result)
        || !Registry::get('runtime.full_page_cache.inited')
        || stripos($query, 'SELECT') === false
        || !fn_full_page_cache_is_current_dispatch_cacheable()
    ) {
        return;
    }

    static $table_prefix;

    if ($table_prefix === null) {
        $table_prefix = Registry::get('config.table_prefix');
    }

    preg_match_all('/(?:FROM|JOIN)\s+(?<tables>[\-_\d\w]+)/i', $query, $matches);

    if (empty($matches['tables'])) {
        return;
    }

    $matches['tables'] = array_map(static function ($table) use ($table_prefix) {
        return str_replace($table_prefix, '', $table);
    }, $matches['tables']);

    /** @var Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];
    $addon->registerPageCacheTags($matches['tables']);
}

/**
 * The "sucess_user_login" hook handler.
 *
 * Actions performed:
 *  - Prevents usage full page cache for authorized user by set cookie.
 *
 * @see fn_login_user
 *
 * @return void
 */
function fn_full_page_cache_sucess_user_login()
{
    if (AREA === SiteArea::STOREFRONT) {
        fn_full_page_cache_disable_cache_by_cookie();
    }
}

/**
 * The "user_logout_after" hook handler.
 *
 * Actions performed:
 *  - Enables usage full page cache after user is logout by remove cookie
 *
 * @see fn_user_logout
 *
 * @return void
 */
function fn_full_page_cache_user_logout_after()
{
    if (AREA === SiteArea::STOREFRONT) {
        fn_full_page_cache_enable_cache_by_cookie();
    }
}

/**
 * The "update_customization_mode" hook handler.
 *
 * @param array<string> $modes         Modes
 * @param array<string> $enabled_modes Enabled modes
 *
 * Actions performed:
 *  - Prevents usage full page cache for customization mode
 *
 * @see fn_update_customization_mode
 *
 * @return void
 */
function fn_full_page_cache_update_customization_mode(array $modes, array $enabled_modes)
{
    if (empty($enabled_modes)) {
        fn_full_page_cache_enable_cache_by_cookie();
    } else {
        fn_full_page_cache_disable_cache_by_cookie();
    }
}

/**
 * The "dispatch_before_display" hook handler.
 *
 * Actions performed:
 *  - If notifications exists then marks the current request for disable caching.
 *
 * @see fn_dispatch
 *
 * @return void
 */
function fn_full_page_cache_dispatch_before_display()
{
    if (AREA !== SiteArea::STOREFRONT) {
        return;
    }

    Registry::set('runtime.full_page_cache.notification_exists', !empty(Tygh::$app['session']['notifications']));
}
