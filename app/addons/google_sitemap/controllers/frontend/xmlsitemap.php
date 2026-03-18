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

if ($mode === 'view') {
    /** @var \Tygh\Storefront\Storefront $storefront */
    $storefront = Tygh::$app['storefront'];
    $page = isset($_REQUEST['page'])
        ? (int) $_REQUEST['page']
        : null;

    $sitemap_file_path = fn_google_sitemap_get_sitemap_path($storefront->storefront_id, $page);

    if (file_exists($sitemap_file_path)) {
        header('Content-Type: text/xml;charset=utf-8');
        readfile($sitemap_file_path);
        exit();
    }

    return [CONTROLLER_STATUS_NO_PAGE];
}
