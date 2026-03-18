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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view') {
    $product = Tygh::$app['view']->getTemplateVars('product');

    foreach ($product['header_features'] as $feature_id => $feature_data) {
        if ($feature_data['feature_type'] == 'E') {
            Tygh::$app['view']->assign('ym_brand', $feature_data['variant']);
            break;
        }
    }

    Tygh::$app['view']->assign('category', fn_get_category_path($product['main_category']));

    if (!empty($product['selected_options'])) {
        $variants_name = array();
        foreach ($product['selected_options'] as $option_id => $option_variant_id) {
            $option_data = fn_get_product_option_data($option_id, $product['product_id']);

            if (isset($option_data['variants'][$option_variant_id]['variant_name'])) {
                if (!empty($option_data['variants'][$option_variant_id]['yml2_variant'])) {
                    $variants_name[] = $option_data['variants'][$option_variant_id]['yml2_variant'];
                } else {
                    $variants_name[] = $option_data['variants'][$option_variant_id]['variant_name'];
                }
            }
        }
        $variants_name = implode(', ', $variants_name);

        Tygh::$app['view']->assign('ym_variant', $variants_name);
    }

} elseif ($mode == 'quick_view') {
    if (defined('AJAX_REQUEST') && !empty($_REQUEST['product_id'])) {
        $product = Tygh::$app['view']->getTemplateVars('product');
        fn_rus_yandex_metrika_get_additional_information($product, array());
    }
}
