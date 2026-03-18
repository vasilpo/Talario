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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode === 'login_provider' || $mode === 'link_provider') {
    $status = fn_hybrid_auth_process($mode, $redirect_url);

    if ($status === HYBRID_AUTH_LOADING) {
        Tygh::$app['view']->display('addons/hybrid_auth/views/auth/loading.tpl');
    } else {
        unset(Tygh::$app['session']['cart']['edit_step']);
        Tygh::$app['view']->assign('redirect_url', $redirect_url);
        Tygh::$app['view']->display('addons/hybrid_auth/views/auth/login_error.tpl');
    }

    exit;
} elseif ($mode === 'process') {
    try {
        $hybridauth = fn_hybrid_auth_init();
        $redirect_url = !empty(Tygh::$app['session']['hybrid_auth']['return_to_url'])
            ? Tygh::$app['session']['hybrid_auth']['return_to_url']
            : fn_url();

        if (!empty($provider = Tygh::$app['session']['hybrid_auth']['provider'])) {
            $hybridauth->authenticate($provider);
            unset(Tygh::$app['session']['hybrid_auth']['provider']);
        }

        return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
    } catch (Exception $e) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
        Tygh::$app['view']->display('addons/hybrid_auth/views/auth/login_error.tpl');

        exit;
    }

} elseif ($mode === 'logout') {
    // Remove Hybrid auth data
    unset(Tygh::$app['session']['HYBRIDAUTH::STORAGE']);

} elseif ($mode === 'connect_social') {
    $email = !empty(Tygh::$app['session']['hybrid_auth']['email']) ? Tygh::$app['session']['hybrid_auth']['email'] : '';
    $identifier = !empty(Tygh::$app['session']['hybrid_auth']['identifier']) ? Tygh::$app['session']['hybrid_auth']['identifier'] : '';
    $provider_id = !empty(Tygh::$app['session']['hybrid_auth']['provider_id']) ? Tygh::$app['session']['hybrid_auth']['provider_id'] : 0;
    $redirect_url = !empty(Tygh::$app['session']['hybrid_auth']['redirect_url']) ? Tygh::$app['session']['hybrid_auth']['redirect_url'] : fn_url();

    if (!empty(Tygh::$app['session']['auth']['user_id'])) {

        fn_hybrid_auth_link_provider(Tygh::$app['session']['auth']['user_id'], $identifier, $provider_id);
        unset(Tygh::$app['session']['hybrid_auth']);

        return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
    }

    if (AREA !== SiteArea::ADMIN_PANEL) {
        fn_add_breadcrumb(__('hybrid_auth.connect_social'));
    }

    $user_id = fn_is_user_exists(0, ['email' => $email]);

    if (!empty($user_id)) {
        $user_data = fn_get_user_short_info((int) $user_id);

        $user_login = $user_data['email'];
    } else {
        $user_login = '';
    }

    Tygh::$app['view']->assign('user_login', $user_login);
    Tygh::$app['view']->assign('identifier', $identifier);
    Tygh::$app['view']->assign('view_mode', 'simple');

} elseif ($mode === 'specify_email') {
    if (!empty($_REQUEST['user_email'])) {
        fn_hybrid_auth_process('login_provider', $redirect_url);
        $_REQUEST['redirect_url'] = $redirect_url;

        return [CONTROLLER_STATUS_REDIRECT, fn_url($redirect_url)];
    }

    $identifier = !empty(Tygh::$app['session']['hybrid_auth']['identifier']) ? Tygh::$app['session']['hybrid_auth']['identifier'] : '';
    $provider_id = !empty(Tygh::$app['session']['hybrid_auth']['provider_id']) ? Tygh::$app['session']['hybrid_auth']['provider_id'] : 0;
    $redirect_url = !empty(Tygh::$app['session']['hybrid_auth']['redirect_url']) ? Tygh::$app['session']['hybrid_auth']['redirect_url'] : fn_url();

    if (AREA !== SiteArea::ADMIN_PANEL) {
        fn_add_breadcrumb(__('hybrid_auth.specify_email'));
    }

    Tygh::$app['view']->assign('identifier', $identifier);
    Tygh::$app['view']->assign('provider_id', $provider_id);
    Tygh::$app['view']->assign('redirect_url', $redirect_url);
    Tygh::$app['view']->assign('view_mode', 'simple');

}
