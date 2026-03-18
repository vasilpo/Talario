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
use Tygh\Tygh;
use Tygh\Ym\Yml2;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'generate' || $mode == 'get') {

    $access_key = !empty($_REQUEST['access_key']) ? $_REQUEST['access_key'] : '';
    $price_id = !empty($_REQUEST['price_id']) ? $_REQUEST['price_id'] : 0;

    if (empty($price_id) && !empty($access_key)) {
        $price_id = fn_yml_get_price_id($access_key);
    }

    $options = fn_yml_get_options($price_id);

    if (!empty($options) && $options['enable_authorization'] == 'Y' && empty($access_key)) {
        $options = array();
    }

    if (!empty($options)) {
        $company_id = isset($options['company_id']) ? $options['company_id'] : Registry::get('runtime.company_id');

        $offset = !empty($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

        $lang_code = DESCR_SL;
        if (Registry::isExist('languages.ru')) {
            $lang_code = 'ru';
        }
        if (!isset($options['storefront_id'])) {
            $options['storefront_id'] = Registry::get('runtime.storefront_id');
        }

        $storefront_repository = Tygh::$app['storefront.repository'];
        $storefront = $storefront_repository->findById($options['storefront_id']);
        $options['storefront_url'] = $storefront->url;

        $yml = new Yml2($company_id, $price_id, $lang_code, $offset, isset($_REQUEST['debug']), $options);

        if ($mode == 'get') {
            $yml->get();

        } else {
            $yml->generate();
        }
    } else {
        fn_echo(__("error"));
    }

    exit;
}
