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

/** @var string $controller */

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view') {

    $restored_send_data = fn_restore_post_data('send_data');
    if (!empty($restored_send_data)) {
        Tygh::$app['view']->assign('send_data', $restored_send_data);
    }

    $params = [
        'object_id' => (int) $_REQUEST['page_id'],
        'object'    => $controller,
        'lang_code' => CART_LANGUAGE,
    ];

    $provider_settings = fn_get_sb_provider_settings($params);

    Tygh::$app['view']->assign('display_button_block', fn_sb_display_block($provider_settings));
    Tygh::$app['view']->assign('provider_settings', $provider_settings);
    Tygh::$app['view']->assign('provider_meta_data', fn_get_sb_providers_meta_data($params));
}
