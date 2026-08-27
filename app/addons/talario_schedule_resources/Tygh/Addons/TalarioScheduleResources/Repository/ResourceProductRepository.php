<?php

namespace Tygh\Addons\TalarioScheduleResources\Repository;

class ResourceProductRepository
{
    public function exists($product_id, $resource_id) { return (bool) db_get_field('SELECT 1 FROM ?:talario_resource_products WHERE product_id = ?i AND resource_id = ?i', $product_id, $resource_id); }
    public function add($product_id, $resource_id) { db_query('INSERT INTO ?:talario_resource_products ?e', ['product_id' => $product_id, 'resource_id' => $resource_id]); }
    public function remove($product_id, $resource_id) { db_query('DELETE FROM ?:talario_resource_products WHERE product_id = ?i AND resource_id = ?i', $product_id, $resource_id); }
    public function findResourcesByProduct($product_id, $company_id)
    {
        return db_get_fields(
            'SELECT mapping.resource_id FROM ?:talario_resource_products AS mapping'
            . ' INNER JOIN ?:talario_resources AS resources ON resources.resource_id = mapping.resource_id'
            . ' INNER JOIN ?:products AS products ON products.product_id = mapping.product_id'
            . ' WHERE mapping.product_id = ?i AND resources.company_id = ?i AND products.company_id = ?i',
            $product_id,
            $company_id,
            $company_id
        );
    }

    public function findProductsByResource($resource_id, $company_id)
    {
        return db_get_fields(
            'SELECT mapping.product_id FROM ?:talario_resource_products AS mapping'
            . ' INNER JOIN ?:talario_resources AS resources ON resources.resource_id = mapping.resource_id'
            . ' INNER JOIN ?:products AS products ON products.product_id = mapping.product_id'
            . ' WHERE mapping.resource_id = ?i AND resources.company_id = ?i AND products.company_id = ?i',
            $resource_id,
            $company_id,
            $company_id
        );
    }
    public function findProduct($product_id) { return db_get_row('SELECT product_id, company_id FROM ?:products WHERE product_id = ?i', $product_id) ?: null; }
}
