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

class ProductsHookHandler
{
    /**
     * The 'get_products' hook handler
     * Changes additional params for selecting products
     *
     * @param array  $params    Product search params
     * @param array  $fields    List of fields for retrieving
     * @param array  $sortings  Sorting fields
     * @param string $condition String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
     * @param string $join      String with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param string $sorting   String containing the SQL-query ORDER BY clause
     * @param string $group_by  String containing the SQL-query GROUP BY field
     * @param string $lang_code Two-letter language code (e.g. 'en', 'ru', etc.)
     * @param array  $having    HAVING condition
     *
     * @return void
     * @see \fn_get_products()
     */
    public static function getProducts($params, &$fields, $sortings, $condition, $join, $sorting, $group_by, $lang_code, $having): void
    {
        if (in_array('product_name', $params['extend']) || in_array('description', $params['extend'])) {
            $fields['short_description'] = 'descr1.short_description';
            $fields['address'] = 'descr1.address';
        }
    }
}
