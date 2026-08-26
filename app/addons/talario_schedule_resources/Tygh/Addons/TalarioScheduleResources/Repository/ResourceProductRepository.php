<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class ResourceProductRepository
{
    public function exists($product_id, $resource_id) { return (bool) db_get_field('SELECT 1 FROM ?:talario_resource_products WHERE product_id = ?i AND resource_id = ?i', $product_id, $resource_id); }
    public function add($product_id, $resource_id) { db_query('INSERT INTO ?:talario_resource_products ?e', ['product_id' => $product_id, 'resource_id' => $resource_id]); }
    public function remove($product_id, $resource_id) { db_query('DELETE FROM ?:talario_resource_products WHERE product_id = ?i AND resource_id = ?i', $product_id, $resource_id); }
    public function findResourcesByProduct($product_id) { return db_get_fields('SELECT resource_id FROM ?:talario_resource_products WHERE product_id = ?i', $product_id); }
    public function findProductsByResource($resource_id) { return db_get_fields('SELECT product_id FROM ?:talario_resource_products WHERE resource_id = ?i', $resource_id); }
    public function findProduct($product_id) { return db_get_row('SELECT product_id, company_id FROM ?:products WHERE product_id = ?i', $product_id) ?: null; }
}
