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

use Tygh\Enum\NotificationSeverity;
use Tygh\Helpdesk;

/**
 * @var string $mode
 * @var string $action
 */

if (ACCOUNT_TYPE !== 'admin') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'check') {
    if (!fn_backend_google_auth_is_configured()) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('backend_google_auth.errors.not_configured'));

        return [CONTROLLER_STATUS_REDIRECT, 'addons.manage'];
    }

    fn_set_session_data('backend_google_auth.is_check', true);
    fn_backend_google_auth_hybrid_auth_authenticate();
    exit();
} elseif ($mode === 'callback') {
    try {
        $hybrid_auth = fn_backend_google_auth_create_hybrid_auth_instance();

        $hybrid_auth->authenticate(BACKEND_GOOGLE_AUTH_PROVIDER);

        return [CONTROLLER_STATUS_REDIRECT, fn_url('backend_google_auth.done')];
    } catch (Exception $exception) {
        fn_delete_session_data('backend_google_auth.is_check');
        fn_set_notification(NotificationSeverity::ERROR, __('error'), $exception->getMessage());
    }

    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
} elseif ($mode === 'done') {
    $return_url = $_REQUEST['return_url'] ?? fn_url();

    try {
        $hybrid_auth = fn_backend_google_auth_create_hybrid_auth_instance();

        if ($hybrid_auth->isConnectedWith(BACKEND_GOOGLE_AUTH_PROVIDER)) {
            $adapter = $hybrid_auth->getAdapter(BACKEND_GOOGLE_AUTH_PROVIDER);

            $profile = $adapter->getUserProfile();

            if ($profile && !empty($profile->email)) {
                $email = trim($profile->email);

                $user_id = fn_backend_google_auth_find_active_user_by_email($email);

                if ($user_id && fn_login_user($user_id, true) === LOGIN_STATUS_OK) {
                    Helpdesk::auth();
                    fn_log_event('users', 'session', [
                        'user_id' => $user_id
                    ]);

                    if (fn_get_session_data('backend_google_auth.is_check')) {
                        fn_delete_session_data('backend_google_auth.is_check');
                        fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('successful'));
                    }
                    return [CONTROLLER_STATUS_REDIRECT, $return_url];
                } else {
                    fn_set_notification(NotificationSeverity::ERROR, __('error'), __('backend_google_auth.user_not_found', ['[user]' => $email]));
                }
            }
        }
    } catch (Exception $exception) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), $exception->getMessage());
    }

    return [CONTROLLER_STATUS_REDIRECT, 'auth.login_form'];
}