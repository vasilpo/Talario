<?php

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'search') {
    require_once dirname(__DIR__, 2) . '/related.php';
    $view = Tygh::$app['view'];
    $search = (array) $view->getTemplateVars('search');
    $products = (array) $view->getTemplateVars('products');
    $view->assign('talario_related_lessons', fn_talario_search_relevance_related_products($search, $products, CART_LANGUAGE));
}
