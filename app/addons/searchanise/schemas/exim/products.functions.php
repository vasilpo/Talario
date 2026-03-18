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

function fn_searchanise_add_product_actions($primary_object_ids)
{
    if (empty($primary_object_ids) || !is_array($primary_object_ids)) {
        return true;
    }
    $product_ids = [];

    // Add actions for all updated products.
    foreach ($primary_object_ids as $k => $v) {
        if (!empty($v['product_id'])) {
            $product_ids[] = $v['product_id'];
        }
    }
    
    fn_se_add_chunk_product_action('update', $product_ids);

    return true;
}