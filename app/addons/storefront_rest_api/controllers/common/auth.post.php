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

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode == 'token_login' && !empty($_REQUEST['token']) && AREA == 'C') {

    $ekey = $_REQUEST['token'];
    $redirect_url = !empty($_REQUEST['redirect_url']) ? $_REQUEST['redirect_url'] : '';

    $token = fn_get_ekeys(array('ekey' => $ekey, 'object_type' => 'U', 'ttl' => TIME));

    if ($token) {
        $token = reset($token);

        $result = fn_login_user($token['object_id'], true);

        if (!is_null($result)) {
            if ($result === LOGIN_STATUS_USER_NOT_FOUND || $result === LOGIN_STATUS_USER_DISABLED || $result === false) {
                $redirect_url = '';
            } else {
                fn_delete_notification('notice_text_change_password');
            }
        }
    } else {
        fn_set_notification('E', __('error'), __('error_incorrect_login'));
    }

    return array(CONTROLLER_STATUS_REDIRECT, fn_url($redirect_url));
}
