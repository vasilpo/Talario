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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // Delete products from the cart
    //
    if ($mode == 'delete') {

        foreach ($_REQUEST['cart_ids'] as $cart_id) {
            if (isset($cart['products'][$cart_id])) {
                $product = $cart['products'][$cart_id];
                if (!empty($product['extra']['exclude_from_calculate']) && $product['extra']['exclude_from_calculate'] == GIFT_CERTIFICATE_EXCLUDE_PRODUCTS) {
                    $cart['deleted_exclude_products'][GIFT_CERTIFICATE_EXCLUDE_PRODUCTS][$cart_id] = array(
                        'product_id' => $product['product_id'],
                        'in_use_certificate' => $product['extra']['in_use_certificate']
                    );
                }

                if (isset($product['extra']['parent']['certificate'])) {
                    unset($cart['gift_certificates'][$product['extra']['parent']['certificate']]['products'][$product['product_id']]);
                }
            }
        }
    }
}
