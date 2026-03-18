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

use Tygh\Enum\SiteArea;
use Tygh\Tools\Url;
use Tygh\Enum\UserTypes;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * @var string $mode
 */

if ($mode === 'login_as_vendor' || $mode === 'login_as_admin') {
    if ($mode === 'login_as_vendor') {
        $area = SiteArea::VENDOR_PANEL;
        $user_type = UserTypes::VENDOR;
    } else {
        $area = SiteArea::ADMIN_PANEL;
        $user_type = UserTypes::ADMIN;
    }

    if (
        (!empty($_REQUEST['area']) && $_REQUEST['area'] !== SiteArea::STOREFRONT)
        || !Tygh::$app['session']['auth']['user_type']
        || Tygh::$app['session']['auth']['user_type'] !== $user_type
        || empty(Tygh::$app['session']['auth']['user_id'])
    ) {
        if (
            defined('THEMES_PANEL')
            && !empty($_REQUEST['url']) && !empty($_REQUEST['area'])
        ) {
            $redirect_url = Url::buildUrn('bottom_panel.redirect', [
                'url' => $_REQUEST['url'],
                'area' => $_REQUEST['area']
            ]);

            return [CONTROLLER_STATUS_REDIRECT, fn_url($redirect_url, $area)];
        } else {
            return [CONTROLLER_STATUS_NO_PAGE];
        }
    }

    $user_id = Tygh::$app['session']['auth']['user_id'];
    $email = fn_get_user_email($user_id);

    if (empty($email)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $ekey = fn_generate_ekey($user_id, RECOVERY_PASSWORD_EKEY_TYPE, QUICK_LOGIN_PASSWORD_TTL);

    $redirect_url = Url::buildUrn('bottom_panel.redirect', [
        'url'     => !empty($_REQUEST['url']) ? $_REQUEST['url'] : '',
        'user_id' => $user_id,
        'ekey'    => $ekey,
        'area'    => AREA
    ]);

    return [CONTROLLER_STATUS_REDIRECT, fn_url($redirect_url, $area), true];
}
