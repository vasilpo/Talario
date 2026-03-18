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

use Tygh\Addons\TildaPages\ServiceProvider;
use Tygh\Enum\SiteArea;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($mode === 'update') {
        $tilda_client = ServiceProvider::getTildaClient();

        $tilda_project_list = $tilda_client->getProjectsList();

        Tygh::$app['view']->assign('tilda_project_list', $tilda_project_list);
        Tygh::$app['view']->assign('auto_sync_link', fn_url('tilda_pages.import', SiteArea::STOREFRONT));
    }
}
