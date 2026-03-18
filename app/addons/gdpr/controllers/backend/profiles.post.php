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
    return;
}

if ($mode == 'update') {

    if (!empty($_REQUEST['user_id'])
        && isset($_REQUEST['user_type']) && $_REQUEST['user_type'] === 'C'
    ) {
        Registry::set('navigation.tabs.gdpr_user_data', array(
            'title'        => __('gdpr.gdpr_user_data'),
            'href'         => sprintf('gdpr.get_user_data?user_id=%s', $_REQUEST['user_id']),
            'ajax'         => true,
            'ajax_onclick' => true,
        ));

        /** @var \Tygh\Addons\Gdpr\Service $service */
        $service = Tygh::$app['addons.gdpr.service'];
        $anonymized = $service->isUserAnonymized($_REQUEST['user_id']);

        Tygh::$app['view']->assign('anonymized', $anonymized);
    }
}
