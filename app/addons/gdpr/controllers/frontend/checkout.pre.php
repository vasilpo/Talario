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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_steps') {

        if (empty($auth['user_id'])
            && isset($_REQUEST['gdpr_agreements']['checkout_profiles_update'])
            && $_REQUEST['gdpr_agreements']['checkout_profiles_update'] == 'Y'
        ) {
            $params = array(
                'email' => isset($_REQUEST['user_data']['email']) ? $_REQUEST['user_data']['email'] : '',
            );

            fn_gdpr_save_user_agreement($params, 'checkout_profiles_update');
        }
        return [CONTROLLER_STATUS_OK];
    }

    if ($mode == 'update_profile' || $mode == 'place_order') {

        if (!empty($auth['user_id'])
            && isset($_REQUEST['gdpr_agreements']['checkout_profiles_update'])
            && $_REQUEST['gdpr_agreements']['checkout_profiles_update'] == \Tygh\Enum\YesNo::YES
        ) {
            $params = [
                'email' => isset(Tygh::$app['session']['cart']['user_data']['email']) ? Tygh::$app['session']['cart']['user_data']['email'] : '',
                'user_id' => $auth['user_id'],
            ];

            fn_gdpr_save_user_agreement($params, 'checkout_profiles_update');
        }
        return [CONTROLLER_STATUS_OK];
    }
}

