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


/**
 * @var string $mode
 * @var string $action
 */

if (ACCOUNT_TYPE !== 'admin' || !fn_backend_google_auth_is_configured()) {
    return [CONTROLLER_STATUS_OK];
}

if (in_array($mode, ['recover_password', 'password_change', 'change_login', 'ekey_login'], true)) {
    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'login') {
        fn_backend_google_auth_hybrid_auth_authenticate();
        exit();
    }
}

return [CONTROLLER_STATUS_OK];