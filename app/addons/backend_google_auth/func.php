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

use Hybridauth\Hybridauth;
use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;

/**
 * Creates and configures Hybrid_Auth instance
 *
 * @return Hybridauth
 */
function fn_backend_google_auth_create_hybrid_auth_instance()
{
    return new Hybridauth([
        'callback' => fn_url('backend_google_auth.callback?hauth.done=Google'),
        'providers' => [
            'Google' => [
                'enabled' => true,
                'keys' => [
                    'id' => fn_backend_google_auth_get_app_client_id(),
                    'secret' => fn_backend_google_auth_get_app_client_secret()
                ],
                'scope' => 'https://www.googleapis.com/auth/userinfo.email',
                'access_type' => 'offline',
                'approval_prompt' => 'force',
            ]
        ],
    ]);
}

/**
 * Checks if add-on is configured
 *
 * @return bool
 */
function fn_backend_google_auth_is_configured()
{
    return fn_backend_google_auth_get_app_client_id() && fn_backend_google_auth_get_app_client_secret();
}

/**
 * Gets google app client id
 *
 * @return string
 */
function fn_backend_google_auth_get_app_client_id()
{
    $client_id = Registry::get('addons.backend_google_auth.backend_google_auth_client_id');

    return trim($client_id);
}

/**
 * Gets google app client secret
 *
 * @return string
 */
function fn_backend_google_auth_get_app_client_secret()
{
    $client_secret = Registry::get('addons.backend_google_auth.backend_google_auth_client_secret');

    return trim($client_secret);
}

/**
 * Start authenticate user
 *
 * @param string $return_url
 */
function fn_backend_google_auth_hybrid_auth_authenticate()
{
    try {
        $hybrid_auth = fn_backend_google_auth_create_hybrid_auth_instance();
        unset(Tygh::$app['session']['HYBRIDAUTH::STORAGE']);

        $hybrid_auth->authenticate(BACKEND_GOOGLE_AUTH_PROVIDER);
    } catch (Exception $exception) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), $exception->getMessage());
    }
}

/**
 * Finds the identifier of active user by email
 *
 * @param string $user_email
 *
 * @return int
 */
function fn_backend_google_auth_find_active_user_by_email($user_email)
{
    $user_email = (string) $user_email;

    return (int) db_get_field(
        'SELECT user_id FROM ?:users WHERE email = ?s AND user_type = ?s AND status = ?s',
        $user_email, 'A', 'A'
    );
}

/**
 * Hook handler: after user update
 *
 * Actions performed:
 *  - Checks email for available to Sign-In via Google
 */
function fn_backend_google_auth_update_profile($action, $user_data, $current_user_data)
{
    if (AREA !== 'A') {
        return;
    }

    if (!empty($user_data['email'])
        && !empty($user_data['user_type'])
        && $user_data['user_type'] === 'A'
        && strpos($user_data['email'], '@gmail.com') === false
    ) {
        fn_set_notification('W', __('warning'), __('backend_google_auth.warnings.only_gmail_address_is_available_to_auth'));
    }
}

/**
 * Hook handler: after user login
 *
 * Actions performed:
 *  - Updates password_change_timestamp to avoid account_password_expiration_period and change_admin_password_on_first_login settings
 */
function fn_backend_google_auth_login_user_post($user_id, $cu_id, $udata, &$auth, $condition, $result)
{
    if (AREA !== 'A' || $result != LOGIN_STATUS_OK || $udata['user_type'] !== 'A') {
        return;
    }

    $expire = Registry::get('settings.Security.account_password_expiration_period') * SECONDS_IN_DAY;

    if (
        !empty($expire)
        && (
            empty($udata['password_change_timestamp'])
            || (TIME - $udata['password_change_timestamp']) > $expire
        )
    ) {
        $auth['password_change_timestamp'] = TIME;
    } else {
        $auth['password_change_timestamp'] = $udata['password_change_timestamp'];
    }

    db_query('UPDATE ?:users SET password_change_timestamp = ?i WHERE user_id = ?i', $auth['password_change_timestamp'], $auth['user_id']);
}