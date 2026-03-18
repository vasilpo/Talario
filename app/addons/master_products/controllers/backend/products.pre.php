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

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Enum\YesNo;

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'export_range') {
        if (empty(Tygh::$app['session']['export_ranges'])) {
            Tygh::$app['session']['export_ranges'] = [];
        }
        if (empty(Tygh::$app['session']['export_ranges']['products']['pattern_id'])) {
            Tygh::$app['session']['export_ranges']['products'] = ['pattern_id' => 'products'];
        }

        if ($action === 'master') {
            if (!empty($_REQUEST['product_ids'])) {
                Tygh::$app['session']['export_ranges']['products']['data'] = ['product_id' => $_REQUEST['product_ids']];
            } elseif (!empty($_REQUEST['master_product_ids'])) {
                Tygh::$app['session']['export_ranges']['products']['data'] = ['product_id' => $_REQUEST['master_product_ids']];
            }
            unset($_REQUEST['redirect_url'], Tygh::$app['session']['export_ranges']['products']['data_provider']);

            return [CONTROLLER_STATUS_REDIRECT, 'exim.export?section=products&pattern_id=products'];
        }
    }
}

if (Registry::get('runtime.company_id') &&
    !YesNo::toBool(Registry::get('addons.master_products.allow_vendors_to_create_products')) &&
    empty($_REQUEST['product_id']) &&
    ($mode === 'add' || $mode === 'update' || $mode === 'm_add')
) {
    return [CONTROLLER_STATUS_REDIRECT, 'products.master_products'];
}
