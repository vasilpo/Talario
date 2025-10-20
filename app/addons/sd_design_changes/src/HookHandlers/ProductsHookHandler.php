<?php
/***************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\SdDesignChanges\HookHandlers;

use Tygh\Enum\SiteArea;

class ProductsHookHandler
{
    /**
     * The 'get_products_post' hook handler
     * Changes selected products
     *
     * @param array  $products  Array of products
     * @param array  $params    Product search params
     * @param string $lang_code Language code
     *
     * @return void
     * @see \fn_get_products()
     */
    public static function getProductsPost(&$products, $params, $lang_code): void
    {
        if ($products && SiteArea::isStorefront(AREA)) {

            $company_address = db_get_hash_array('SELECT company_id, city, address FROM ?:companies WHERE company_id IN (?n)',
                'company_id',
                array_unique(array_column($products, 'company_id'))
            );

            $product_ids = array_column($products, 'product_id');
            $short_descriptions = db_get_hash_single_array(
                'SELECT product_id, short_description FROM ?:product_descriptions WHERE product_id IN (?n) AND lang_code = ?s',
                ['product_id', 'short_description'],
                $product_ids,
                $lang_code
            );

            foreach ($products as &$product) {
                if (!empty($product['company_id'])
                    && (!empty($company_address[$product['company_id']]['city']) || !empty($company_address[$product['company_id']]['address']))
                ) {
                    $city = trim($company_address[$product['company_id']]['city'] ?? '');
                    $address = trim($company_address[$product['company_id']]['address'] ?? '');

                    $product['address'] = $city . ($city && $address ? ', ' : '') . $address;
                }

                if (!empty($short_descriptions[$product['product_id']])) {
                    $product['short_description'] = $short_descriptions[$product['product_id']];
                }
            }
        }
    }
}