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
/** @var string $action */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update' && $action == 'get_auth_token') {

        if ($user_id = Tygh::$app['session']['auth']['user_id']) {

            list($token) = fn_get_user_auth_token($user_id);

            if (!empty($_REQUEST['return_url'])) {
                $_REQUEST['return_url'] = fn_link_attach($_REQUEST['return_url'], 'token=' . $token);

                return array(CONTROLLER_STATUS_OK, $_REQUEST['return_url']);
            }
        }
    }

    return array(CONTROLLER_STATUS_OK);
}