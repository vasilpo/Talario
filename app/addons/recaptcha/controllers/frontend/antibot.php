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

use Tygh\Addons\Recaptcha\RecaptchaDriver;
use Tygh\Registry;
use Tygh\Enum\YesNo;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($mode == 'valid_recaptcha') {
        if (!isset($_REQUEST[RecaptchaDriver::RECAPTCHA_V3_TOKEN_PARAM_NAME])) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        /** @var \Tygh\Web\Antibot $antibot */
        $antibot = Tygh::$app['antibot'];

        $driver = $antibot->getDriver();

        if ($driver instanceof RecaptchaDriver) {
            $driver->validateHttpRequest($_REQUEST);
            return [CONTROLLER_STATUS_OK];
        } else {
            return [CONTROLLER_STATUS_NO_PAGE];
        }
    }

    return;
}

