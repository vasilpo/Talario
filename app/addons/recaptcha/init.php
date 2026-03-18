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

use Tygh\Application;
use Tygh\Registry;
use Tygh\Web\Antibot;

$addons_dir = Registry::get('config.dir.addons');
Tygh::$app['class_loader']->add('Gregwar\\', $addons_dir . '/recaptcha/lib');

Tygh::$app->extend('antibot', function(Antibot $antibot, Application $app) {
    $driver = fn_recaptcha_get_captcha_driver();
    if ($driver->isSetUp()) {
        $antibot->setDriver($driver);
    }

    return $antibot;
});