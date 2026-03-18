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

use Tygh\Addons\ProductVariations\Product\Type\Type;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * @var string $mode
 * @var string $action
 * @var array $auth
 */

if ($mode === 'view' || $mode === 'quick_view') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    /** @var array $product */
    $product = $view->getTemplateVars('product');

    if ($product['product_type'] === Type::PRODUCT_TYPE_VARIATION) {
        $parent_product_id = $product['parent_product_id'];

        $is_exist = array_search($product['product_id'], Tygh::$app['session']['recently_viewed_products']);
        unset(Tygh::$app['session']['recently_viewed_products'][$is_exist]);

        fn_add_product_to_recently_viewed($parent_product_id);
    }

    if (
        defined('AJAX_REQUEST')
        && !empty($_REQUEST['product_id'])
        && empty($view->getTemplateVars('redirect_url'))
    ) {
        $view->assign('redirect_url', 'products.view?product_id=' . $_REQUEST['product_id']);
    }
}