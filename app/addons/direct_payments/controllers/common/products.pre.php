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

if ($mode == 'options') {
    /** @var \Tygh\Addons\DirectPayments\Cart\Service $cart_service */
    $cart_service = Tygh::$app['addons.direct_payments.cart.service'];

    $product_id = null;
    if (isset($_REQUEST['cart_products'])) {
        $product = reset($_REQUEST['cart_products']);
        $product_id = (int) $product['product_id'];
    } elseif (isset($_REQUEST['product_data'])) {
        reset($_REQUEST['product_data']);
        $product_id = (int) key($_REQUEST['product_data']);
    }

    $vendor = fn_get_company_by_product_id($product_id);
    $vendor_id = $vendor['company_id'];

    $cart_service->setCurrentVendorId($vendor_id);
}