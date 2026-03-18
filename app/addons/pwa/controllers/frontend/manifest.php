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

if ($mode === 'view') {
    $storefront_id = Tygh::$app['storefront']->storefront_id;
    $version = isset($_REQUEST['v']) && is_string($_REQUEST['v']) ? $_REQUEST['v'] : '';
    $cache_key = 'pwa_manifest_cache_' . $version;

    Registry::registerCache(
        $cache_key,
        SECONDS_IN_DAY,
        Registry::cacheLevel(['time'])
    );
    if (!Registry::isExist($cache_key)) {
        $content = fn_pwa_generate_manifest_content($storefront_id);
        Registry::set($cache_key, $content);
    }

    $content = Registry::get($cache_key);

    if (empty($content)) {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Manifest not found';
        exit;
    }

    header('Content-Type: application/manifest+json; charset=utf-8');
    echo $content;
    exit;
}
