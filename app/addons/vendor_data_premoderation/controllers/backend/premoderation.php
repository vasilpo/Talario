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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reason = '';
    $product_ids = isset($_REQUEST['product_ids'])
        ? $_REQUEST['product_ids']
        : [];

    if (!$product_ids && $action) {
        $product_ids = [$action];
    }

    if (count($product_ids) === 1) {
        $declined_product_id = reset($product_ids);
        $reason = isset($_REQUEST['product_approval'][$declined_product_id]['reason'])
            ? $_REQUEST['product_approval'][$declined_product_id]['reason']
            : '';
    }

    if (!$reason && isset($_REQUEST['product_approval'][0]['reason'])) {
        $reason = $_REQUEST['product_approval'][0]['reason'];
    }

    if (($mode == 'm_approve' || $mode == 'm_decline') && $product_ids) {
        if ($mode == 'm_approve') {
            fn_vendor_data_premoderation_approve_products($product_ids, true);
        } else {
            fn_vendor_data_premoderation_disapprove_products($product_ids, true, $reason);
        }
    }
}
