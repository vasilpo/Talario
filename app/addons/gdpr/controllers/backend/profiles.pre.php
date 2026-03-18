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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /** @var Tygh\Addons\Gdpr\Service $service Gdpr service */
    $service = Tygh::$app['addons.gdpr.service'];

    if ($mode === 'anonymize') {
        if (
            empty($_REQUEST['user_id'])
            || !$service->isUserAnonymizable($_REQUEST['user_id'])
        ) {
            return [CONTROLLER_STATUS_REDIRECT, 'profiles.manage'];
        }
        $service->markUserAsAnonymized($_REQUEST['user_id']);
    }
}
