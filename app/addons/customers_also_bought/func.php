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

function fn_customers_also_bought_place_order(&$order_id)
{

    $product_ids = db_get_fields("SELECT product_id FROM ?:order_details WHERE order_id = ?i GROUP BY product_id", $order_id);

    if (count($product_ids) < 2) {
        return;
    }

    foreach ($product_ids as $_origin) {
        foreach ($product_ids as $_target) {
            if ($_origin != $_target) {
                $amount = db_get_field(
                    'SELECT amount FROM ?:also_bought_products WHERE product_id = ?i AND related_id = ?i',
                    $_origin,
                    $_target
                );

                $_data = [
                    'product_id' => $_origin,
                    'related_id' => $_target,
                    'amount'     => (int) $amount + 1,
                ];

                db_query("REPLACE INTO ?:also_bought_products ?e", $_data);
            }
        }
    }
}

function fn_customers_also_bought_delete_product_post(&$product_id)
{
    db_query("DELETE FROM ?:also_bought_products WHERE product_id = ?i OR related_id = ?i", $product_id, $product_id);

    return true;
}

function fn_customers_also_bought_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by)
{
    if (!empty($params['also_bought_for_product_id']) || !empty($params['also_bought_for_product_ids'])) {
        if (!empty($params['sort_by']) && ($params['sort_by'] == 'amnt')) {
            $sortings['amnt'] = 'SUM(?:also_bought_products.amount)';
        }

        $fields[] = 'SUM(?:also_bought_products.amount) amnt';
        $join .= ' LEFT JOIN ?:also_bought_products ON ?:also_bought_products.related_id = products.product_id ';

        if (isset($params['also_bought_for_product_id'])) {
            $condition .= db_quote(' AND ?:also_bought_products.product_id = ?i', $params['also_bought_for_product_id']);
        } else {
            $condition .= db_quote(' AND ?:also_bought_products.product_id IN (?n)', $params['also_bought_for_product_ids']);
        }

        $group_by = '?:also_bought_products.related_id';
    }

    return true;
}
