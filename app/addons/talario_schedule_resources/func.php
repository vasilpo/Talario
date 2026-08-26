<?php

defined('BOOTSTRAP') or die('Access denied');

/** Removes only the commercial mapping; physical schedule history is retained. */
function fn_talario_schedule_resources_delete_product_post($product_id, $product_deleted)
{
    if ($product_deleted) {
        db_query('DELETE FROM ?:talario_resource_products WHERE product_id = ?i', $product_id);
    }
}
