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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode === 'update' || $mode === 'add') {
    $linked_providers = [];
    if (!empty($auth['user_id'])) {
        $linked_providers = fn_hybrid_auth_get_link_provider($auth['user_id']);
    }

    Tygh::$app['view']->assign('linked_providers', $linked_providers);

} elseif ($mode === 'unlink_provider') {
    if (defined('AJAX_REQUEST')) {
        if (!empty($auth['user_id']) && !empty($_REQUEST['provider_id'])) {
            fn_hybrid_auth_get_unlink_provider($auth['user_id'], $_REQUEST['provider_id']);
        }

        if (!empty($auth['user_id'])) {
            $linked_providers = fn_hybrid_auth_get_link_provider($auth['user_id']);
            Tygh::$app['view']->assign('linked_providers', $linked_providers);
        }

        Tygh::$app['view']->display('views/profiles/update.tpl');
    }

    exit;

} elseif ($mode === 'link_provider') {
    $status = fn_hybrid_auth_process('link_provider_profile', $redirect_url);

    if ($status === HYBRID_AUTH_LOADING) {
        Tygh::$app['view']->display('addons/hybrid_auth/views/auth/loading.tpl');

    } else {
        Tygh::$app['view']->assign('redirect_url', fn_url($redirect_url));
        Tygh::$app['view']->display('addons/hybrid_auth/views/auth/login_error.tpl');
    }

    exit;
}
