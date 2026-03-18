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

use Tygh\Addons\ProductVariations\ServiceProvider;
use Tygh\Addons\ProductVariations\Product\Type\Type;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * @var string $mode
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'm_update') {
        $products_variation_feature_values = isset($_REQUEST['products_variation_feature_values']) ? (array) $_REQUEST['products_variation_feature_values'] : [];
        $variation_group = isset($_REQUEST['variation_group']['id'], $_REQUEST['variation_group']['code']) ? (array) $_REQUEST['variation_group'] : [];

        if ($variation_group) {
            $service = ServiceProvider::getService();

            $result = $service->updateGroupCode($variation_group['id'], $variation_group['code']);
            $result->showNotifications();
        }


        if ($products_variation_feature_values) {
            $group_product_feature_values = [];
            $product_ids = array_keys($products_variation_feature_values);

            $group_repository = ServiceProvider::getGroupRepository();
            $service = ServiceProvider::getService();

            $group_ids = $group_repository->findGroupIdsByProductIds($product_ids);

            foreach ($products_variation_feature_values as $product_id => $feature_values) {
                if (empty($group_ids[$product_id])) {
                    continue;
                }

                $group_id = (int) $group_ids[$product_id];

                $group_product_feature_values[$group_id][$product_id] = $feature_values;
            }

            foreach ($group_product_feature_values as $group_id => $product_feature_values) {
                $result = $service->changeProductsFeatureValues($group_id, $product_feature_values);

                $result->showNotifications();
            }
        }
    }

    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update'
    && fn_allowed_for('MULTIVENDOR')
    && defined('AJAX_REQUEST')
    && isset($_REQUEST['product_id'])
) {
    $product_repository = ServiceProvider::getProductRepository();
    $product_data = $product_repository->findProduct($_REQUEST['product_id']);

    if ($product_data) {
        Tygh::$app['view']->assign('product_type', Type::createByProduct($product_data));
    }
}
