<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'update_cdn' && !empty($_REQUEST['cdn_data'])) {
    $_REQUEST['cdn_data'] = fn_lt_yandex_cdn_static_prepare_cdn_data((array) $_REQUEST['cdn_data']);

    Registry::set(
        'config.cdn_backend',
        fn_lt_yandex_cdn_static_get_backend_by_service(
            fn_lt_yandex_cdn_static_get_selected_service($_REQUEST['cdn_data'])
        )
    );
}
