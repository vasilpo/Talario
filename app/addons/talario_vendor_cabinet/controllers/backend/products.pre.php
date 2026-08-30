<?php

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !$company_id) {
    return;
}

if ($mode === 'add') {
    return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.add'];
}

if ($mode !== 'update') {
    return;
}

$product_id = (int) ($_REQUEST['product_id'] ?? 0);
$product = db_get_row('SELECT product_id, company_id FROM ?:products WHERE product_id = ?i', $product_id);
if (!$product || (int) $product['company_id'] !== $company_id) {
    return [CONTROLLER_STATUS_NO_PAGE];
}

$parent_product_id = (int) db_get_field(
    'SELECT parent_product_id FROM ?:product_variation_group_products WHERE product_id = ?i LIMIT 1',
    $product_id
);
if ($parent_product_id) {
    $parent_company_id = (int) db_get_field('SELECT company_id FROM ?:products WHERE product_id = ?i', $parent_product_id);
    if ($parent_company_id !== $company_id) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }
    $product_id = $parent_product_id;
}

return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.update?product_id=' . $product_id];
