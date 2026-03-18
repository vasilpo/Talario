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

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
if (defined('INSTALLER_INITED') || defined('INSTALLER_STARTED')) {
    return $schema;
}

/** @var \Tygh\Web\Antibot $antibot */
$antibot = Tygh::$app['antibot'];

if (!($antibot->getDriver() instanceof RecaptchaDriver)) {
    return $schema;
}

$schema['services']['recaptcha'] = [
    'purposes' => ['strictly_necessary'],
    'name' => 'recaptcha',
    'translations' => [
        'zz' => [
            'title' => 'recaptcha.recaptcha_cookie_title',
            'description' => 'recaptcha.recaptcha_cookie_description'
        ],
    ],
    'required' => true,
];

return $schema;
