<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'search') {
    $products = (array) Tygh::$app['view']->getTemplateVars('products');
    $search = (array) Tygh::$app['view']->getTemplateVars('search');

    if (!empty($products) || empty($search)) {
        return;
    }

    $recommended_products = fn_lr_design_changes_get_search_recommended_products(
        (string) Registry::get('addons.lr_design_changes.recommended_product_ids')
    );

    if (empty($recommended_products)) {
        return;
    }

    Tygh::$app['view']->assign('lr_design_changes_recommended_products', $recommended_products);
}
