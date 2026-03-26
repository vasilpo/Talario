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
    $redirect_page_id = (int) Registry::get('addons.lr_design_changes.empty_search_redirect_page_id');
    $products = (array) Tygh::$app['view']->getTemplateVars('products');
    $search = (array) Tygh::$app['view']->getTemplateVars('search');

    if ($redirect_page_id <= 0 || !empty($products) || empty($search)) {
        return;
    }

    return [CONTROLLER_STATUS_REDIRECT, 'pages.view?page_id=' . $redirect_page_id];
}
