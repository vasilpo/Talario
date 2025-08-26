<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\Warehouses;

use Tygh\Database\Connection;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Providers\StorefrontProvider;
use Tygh;

class Manager
{
    const STORE_LOCATOR_TYPE_WAREHOUSE = 'W';
    const STORE_LOCATOR_TYPE_STORE = 'S';
    const STORE_LOCATOR_TYPE_PICKUP = 'P';

    /** @var Connection */
    protected $db;

    /** @var string */
    protected $language_code;

    /** @var bool */
    protected $is_mve;

    public function __construct(Connection $db, $language_code, $is_mve)
    {
        $this->db = $db;
        $this->language_code = $language_code;
        $this->is_mve = (bool) $is_mve;
    }

    /**
     * Fetches available warehouses
     *
     * @param int|null $company_id Company identifier
     *
     * @return mixed
     */
    public function getWarehouses($company_id = null)
    {
        $params = ['store_types' => [self::STORE_LOCATOR_TYPE_WAREHOUSE, self::STORE_LOCATOR_TYPE_STORE]];
        if ($company_id !== null) {
            $params['company_id'] = $company_id;
        }

        [$warehouses] = fn_get_store_locations($params, 0, $this->language_code);
        foreach ($warehouses as &$warehouse) {
            $warehouse['warehouse_id'] = $warehouse['store_location_id'];
        }

        return $warehouses;
    }

    /**
     * Creates product store manager
     *
     * @param int   $product_id         Product identifier
     *
     * @return \Tygh\Addons\Warehouses\ProductStock
     */
    public function getProductWarehousesStock($product_id)
    {
        $product_stocks = $this->getProductWarehousesStockBulk([$product_id]);
        return reset($product_stocks);
    }

    /**
     * Creates product store manager
     *
     * @param int[] $product_ids Product identifier
     *
     * @return array<ProductStock>
     */
    public function getProductWarehousesStockBulk(array $product_ids)
    {
        $product_stocks = [];

        $products_warehouses = $this->getMultipleProductsWarehousesData($product_ids);
        $products_is_stock_split_data = $this->areProductStocksSplitByWarehouses($product_ids);

        foreach ($product_ids as $product_id) {
            $product_stocks[] = new ProductStock(
                $product_id,
                $products_warehouses[$product_id] ?? [],
                $products_is_stock_split_data[$product_id]['is_stock_split_by_warehouses']
            );
        }

        return $product_stocks;
    }

    /**
     * Creates product store manager based on prowided warehouses stock data
     *
     * @param int   $product_id         Product identifier
     * @param array $warehouses_amounts Product warehouses amount values
     *
     * @return \Tygh\Addons\Warehouses\ProductStock
     */
    public function createProductStockFromWarehousesData($product_id, $warehouses_amounts)
    {
        $warehouse_ids = array_column($warehouses_amounts, 'warehouse_id');

        if ($warehouse_ids) {
            $warehouses = $this->db->getHash(
                'SELECT store_location_id AS warehouse_id, store_type, position, main_destination_id, pickup_destinations_ids, shipping_destinations_ids, status'
                . ' FROM ?:store_locations'
                . ' WHERE store_location_id IN (?n)',
                'warehouse_id',
                $warehouse_ids
            );

            $valid_warehouse_amounts = array_filter($warehouses_amounts, function ($warehouse) use ($warehouses) {
                return isset($warehouses[$warehouse['warehouse_id']]);
            });

            $product_warehouses = [];
            foreach ($valid_warehouse_amounts as $warehouse) {
                $product_warehouses[] = array_merge($warehouses[$warehouse['warehouse_id']], $warehouse);
            }
        } else {
            $product_warehouses = [];
        }

        $product_warehouses = $this->initializeDestinations($product_warehouses);

        return new ProductStock($product_id, $product_warehouses);
    }

    /**
     * Fetches product warehouses amounts for group of products
     *
     * @param array $products Products
     *
     * @return array
     */
    public function fetchProductsWarehousesAmounts($products)
    {
        if (empty($products)) {
            return $products;
        }

        $product_ids = array_column($products, 'product_id');
        $conditions = [$this->db->quote('?:warehouses_sum_products_amount.product_id IN (?n)', $product_ids)];
        if (!$this->is_mve) {
            $product_ids = $this->db->getColumn(
                'SELECT product_id FROM ?:products WHERE product_id IN (?n) AND is_stock_split_by_warehouses = ?s',
                $product_ids,
                YesNo::YES
            );
            $store = StorefrontProvider::getStorefront();
            if ($store) {
                $conditions = [$this->db->quote('?:warehouses_sum_products_amount.product_id IN (?n)', $product_ids)];
                $conditions[] = $this->db->quote('storefront_id = ?i', $store->storefront_id);
            }
        }
        $products_warehouses_amounts = $this->db->getHash(
            'SELECT product_id, amount FROM ?:warehouses_sum_products_amount WHERE ?p',
            'product_id',
            implode(' AND ', $conditions)
        );

        foreach ($products as &$product) {
            $product_id = $product['product_id'];
            if (isset($products_warehouses_amounts[$product_id])) {
                $product['warehouse_amount'] = $products_warehouses_amounts[$product_id]['amount'];
            } elseif (!$this->is_mve && in_array($product_id, $product_ids)) {
                $product['warehouse_amount'] = 0;
            }
        }

        return $products;
    }

    /**
     * Fetches product warehouses amounts by destination ID for group of products
     *
     * @param array $products       Products
     * @param int   $destination_id Customer destination ID
     * @param int   $storefront_id  Storefront ID
     *
     * @return array
     */
    public function fetchProductsWarehousesAmountsByDestination(array $products, $destination_id, $storefront_id)
    {
        $destination_id = (int) $destination_id;
        $storefront_id = (int) $storefront_id;

        if (empty($products) || !$destination_id || !$storefront_id) {
            return $products;
        }

        if ($this->is_mve) {
            $storefront_id = 0;
        }

        $products = $this->fetchProductsFlagIsStockSplitByWarehouses($products);

        $product_ids = array_column($products, 'product_id');
        $products_amounts = $this->db->getSingleHash(
            'SELECT product_id, amount FROM ?:warehouses_destination_products_amount'
            . ' WHERE product_id IN (?n) AND destination_id = ?i AND storefront_id = ?i',
            ['product_id', 'amount'],
            $product_ids,
            $destination_id,
            $storefront_id
        );

        foreach ($products as &$product) {
            $product_id = $product['product_id'];
            $is_stock_split_by_warehouses = !empty($product['is_stock_split_by_warehouses'])
                && $product['is_stock_split_by_warehouses'] === YesNo::YES;

            if (!$is_stock_split_by_warehouses) {
                continue;
            }

            $product['amount'] = isset($products_amounts[$product_id]) ? (int) $products_amounts[$product_id] : 0;
        }
        unset($product);

        return $products;
    }

    /**
     * Fetches product warehouses total amounts for group of products
     *
     * @param array $products Products
     *
     * @return array
     */
    public function fetchProductsWarehousesTotalAmounts(array $products)
    {
        if (empty($products)) {
            return $products;
        }

        $products = $this->fetchProductsFlagIsStockSplitByWarehouses($products);

        $product_ids = array_column($products, 'product_id');
        $products_amounts = $this->db->getSingleHash(
            'SELECT product_id, SUM(amount) AS amount FROM ?:warehouses_products_amount'
            . ' WHERE product_id IN (?n)'
            . ' GROUP BY product_id',
            ['product_id', 'amount'],
            $product_ids
        );

        foreach ($products as &$product) {
            $product_id = $product['product_id'];
            $is_stock_split_by_warehouses = !empty($product['is_stock_split_by_warehouses'])
                && $product['is_stock_split_by_warehouses'] === YesNo::YES;

            if (!$is_stock_split_by_warehouses) {
                continue;
            }

            $product['amount'] = isset($products_amounts[$product_id]) ? (int) $products_amounts[$product_id] : 0;
        }
        unset($product);

        return $products;
    }

    /**
     * Fetches product detailed warehouses amounts for group of products
     *
     * @param array<int, array<string, string|int|bool>> $products Products
     *
     * @return array<int, array<string, string|int|bool>>
     */
    public function fetchProductsDetailedWarehousesAmounts(array $products)
    {
        if (empty($products)) {
            return $products;
        }

        foreach ($products as &$product) {
            $product_stock = $this->getProductWarehousesStock($product['product_id']);
            $warehouses_amounts = $product_stock->getStockAsArray();
            $product['warehouses_amounts'] = $warehouses_amounts;
        }

        return $products;
    }

    /**
     * Fetches product flag "is_stock_split_by_warehouses" if it not presented
     *
     * @param array $products
     *
     * @return array
     */
    public function fetchProductsFlagIsStockSplitByWarehouses(array $products)
    {
        if (empty($products)) {
            return $products;
        }

        $product_ids = [];

        foreach ($products as $product) {
            if (isset($product['is_stock_split_by_warehouses'])) {
                continue;
            }

            $product_ids[] = $product['product_id'];
        }

        if (empty($product_ids)) {
            return $products;
        }

        $is_stock_split_by_warehouses_map = $this->db->getSingleHash(
            'SELECT product_id, is_stock_split_by_warehouses FROM ?:products WHERE product_id IN (?n)',
            ['product_id', 'is_stock_split_by_warehouses'],
            $product_ids
        );

        foreach ($products as &$product) {
            $product['is_stock_split_by_warehouses'] = isset($is_stock_split_by_warehouses_map[$product['product_id']])
                ? $is_stock_split_by_warehouses_map[$product['product_id']]
                : YesNo::NO;
        }
        unset($product);

        return $products;
    }

    /**
     * Fetches product warehouses amounts for all storefronts
     *
     * @param array<array<string, string|int|bool>> $products Array of products
     *
     * @return array<array<string, string|int|bool>> $products
     */
    public function fetchProductsWarehousesAmountsForAllStorefronts(array $products)
    {
        if (empty($products)) {
            return $products;
        }

        $product_ids = array_column($products, 'product_id');

        $total_amounts_for_all_storefronts = $this->db->getSingleHash(
            'SELECT product_id, amount FROM ?:warehouses_sum_products_amount'
            . ' WHERE product_id IN (?n)'
            . ' AND storefront_id = 0'
            . ' GROUP BY product_id',
            ['product_id', 'amount'],
            $product_ids
        );

        foreach ($products as $p_id => &$product) {
            if (!isset($total_amounts_for_all_storefronts[$p_id])) {
                continue;
            }

            $product['warehouse_amount'] = $total_amounts_for_all_storefronts[$p_id];
        }
        unset($product);

        return $products;
    }

    /**
     * Saves product stock data
     *
     * @param \Tygh\Addons\Warehouses\ProductStock $product_stock Product stock
     * @param bool                                 $remove_all    Remove all records before save
     *
     * @return bool
     */
    public function saveProductStock(ProductStock $product_stock, $remove_all = true)
    {
        return $this->saveProductStocks([$product_stock], $remove_all);
    }

    /**
     * Saves product stock data
     *
     * @param \Tygh\Addons\Warehouses\ProductStock[] $product_stocks Product stocks array
     * @param bool                                   $remove_all     Remove all records before save
     *
     * @return bool
     */
    public function saveProductStocks(array $product_stocks, $remove_all = true)
    {
        if ($remove_all) {
            foreach ($product_stocks as $product_stock) {
                $this->removeProductStocks($product_stock->getProductId());
            }
        }

        $product_ids = [];
        $warehouses_amounts_list = [];
        foreach ($product_stocks as $product_stock) {
            $_product_id = $product_stock->getProductId();

            $product_warehouses_amounts = $product_stock->getStockAsArray();

            if (empty($product_warehouses_amounts)) {
                continue;
            }

            $product_ids[] = $product_stock->getProductId();
            $warehouses_amounts_list[$_product_id] = $product_warehouses_amounts;
        }

        $empty_warehouse_ids = [];

        if (empty($warehouses_amounts_list)) {
            return false;
        }

        foreach ($product_stocks as $product_stock) {
            foreach ($product_stock->getWarehouses() as $product_warehouse) {
                if (!$product_warehouse->isMarkedToRemove()) {
                    continue;
                }

                $product_id = $product_stock->getProductId();
                $warehouse_id = $product_warehouse->getWarehouseId();
                unset($warehouses_amounts_list[$product_id][$warehouse_id]);

                if (empty($warehouses_amounts_list[$product_id])) {
                    unset($warehouses_amounts_list[$product_id]);
                }

                $empty_warehouse_ids[$product_id][$warehouse_id] = $warehouse_id;
            }
        }

        if (!$remove_all && $empty_warehouse_ids) {
            foreach ($empty_warehouse_ids as $product_id => $warehouse_ids) {
                $this->db->query(
                    'DELETE FROM ?:warehouses_products_amount WHERE product_id = ?i AND warehouse_id IN (?n)',
                    $product_id,
                    $warehouse_ids
                );
            }
        }

        $warehouses_amounts_data = [];
        foreach ($warehouses_amounts_list as $warehouses_amounts) {
            foreach ($warehouses_amounts as $warehouse_amount) {
                $warehouses_amounts_data[] = $warehouse_amount;
            }
        }
        if ($warehouses_amounts_data) {
            $this->db->replaceInto('warehouses_products_amount', $warehouses_amounts_data, true);
        }

        $this->recalculateDestinationProductsStocksByProductIds($product_ids);

        $product_stocks_filtered = [];
        foreach ($product_stocks as $product_stock) {
            if (!in_array($product_stock->getProductId(), $product_ids)) {
                continue;
            }

            $product_stocks_filtered[] = $product_stock;
        }

        $this->saveTotalAmounts($product_ids, $product_stocks_filtered, $remove_all);

        return true;
    }

    /**
     * Fetches product warehouses amounts
     *
     * @param int $product_id Product identifier
     *
     * @return array
     */
    public function getProductWarehousesData($product_id)
    {
        $product_warehouses = $this->db->getHash(
            'SELECT warehouse_id, amount, store_type, position, main_destination_id, '
            .   ' pickup_destinations_ids, shipping_destinations_ids, status'
            . ' FROM ?:store_locations AS sl'
            . ' INNER JOIN ?:warehouses_products_amount AS wpa ON wpa.warehouse_id = sl.store_location_id'
            . ' WHERE wpa.product_id = ?i'
            . ' ORDER BY sl.position ASC',
            'warehouse_id',
            $product_id
        );

        $product_warehouses = $this->initializeDestinations($product_warehouses);

        return $product_warehouses;
    }

    /**
     * Fetches multiple products warehouses amounts
     *
     * @param int[] $product_ids Products identifiers
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getMultipleProductsWarehousesData(array $product_ids)
    {
        $products_warehouses = $this->db->getMultiHash(
            'SELECT warehouse_id, amount, store_type, position, main_destination_id,'
            . ' pickup_destinations_ids, shipping_destinations_ids, status, wpa.product_id as product_id'
            . ' FROM ?:store_locations AS sl'
            . ' INNER JOIN ?:warehouses_products_amount AS wpa ON wpa.warehouse_id = sl.store_location_id'
            . ' WHERE wpa.product_id IN (?n)'
            . ' ORDER BY sl.position ASC',
            ['product_id', 'warehouse_id'],
            $product_ids
        );

        $products_warehouses_list = $this->db->getHash(
            'SELECT DISTINCT warehouse_id FROM ?:store_locations AS sl'
            . ' INNER JOIN ?:warehouses_products_amount AS wpa ON wpa.warehouse_id = sl.store_location_id'
            . ' WHERE wpa.product_id IN (?n)',
            'warehouse_id',
            $product_ids
        );

        $products_warehouses_list = $this->initializeDestinations($products_warehouses_list);

        foreach ($products_warehouses as &$product_warehouses) {
            foreach ($product_warehouses as $product_warehouse_id => &$product_warehouse) {
                $product_warehouse['destinations'] = [];
                if (!empty($products_warehouses_list[$product_warehouse_id]['destinations'])) {
                    $product_warehouse['destinations'] = $products_warehouses_list[$product_warehouse_id]['destinations'];
                }
            }
        }

        return $products_warehouses;
    }

    protected function isProductStockSplitByWarehouses($product_id)
    {
        $products_is_stock_split_data = $this->areProductStocksSplitByWarehouses([$product_id]);

        return $products_is_stock_split_data[$product_id]['is_stock_split_by_warehouses'];
    }

    /**
     * Checks if stock is split by warehouses for multiple product stocks.
     *
     * @param int[] $product_ids Products identifiers
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    protected function areProductStocksSplitByWarehouses(array $product_ids)
    {
        $products_is_stock_split_data = $this->db->getHash(
            'SELECT product_id, is_stock_split_by_warehouses FROM ?:products WHERE product_id IN (?n)',
            'product_id',
            $product_ids
        );

        foreach ($products_is_stock_split_data as &$product_data) {
            $product_data['is_stock_split_by_warehouses'] = YesNo::toBool($product_data['is_stock_split_by_warehouses']);
        }
        return $products_is_stock_split_data;
    }

    protected function initializeDestinations(array $product_warehouses)
    {
        if (!$product_warehouses) {
            return [];
        }

        $destinations = $this->db->getMultiHash(
            'SELECT destination_links.*, shipping_delays.*'
            . ' FROM ?:store_location_destination_links AS destination_links'
            . ' LEFT JOIN ?:store_location_shipping_delays AS shipping_delays'
            . ' ON shipping_delays.store_location_id = destination_links.store_location_id'
            . ' AND shipping_delays.destination_id = destination_links.destination_id'
            . ' AND shipping_delays.lang_code = ?s'
            . ' WHERE destination_links.store_location_id IN (?n)',
            ['store_location_id', 'destination_id'],
            $this->language_code,
            array_column($product_warehouses, 'warehouse_id')
        );

        foreach ($product_warehouses as &$warehouse) {
            $warehouse['destinations'] = [];
            if (!empty($destinations[$warehouse['warehouse_id']])) {
                $warehouse['destinations'] = $destinations[$warehouse['warehouse_id']];
            }
        }
        unset($warehouse);

        return $product_warehouses;
    }

    /**
     * @param int $warehouse_id
     *
     * @return \Tygh\Addons\Warehouses\Destination[]
     */
    public function initializeDestinationsByWarehouseId($warehouse_id)
    {
        $warehouses = $this->initializeDestinations([['warehouse_id' => $warehouse_id]]);

        if (!$warehouses) {
            return [];
        }

        $warehouse = reset($warehouses);

        foreach ($warehouse['destinations'] as &$destination) {
            $destination = new Destination($destination);
        }
        unset($destination);

        return $warehouse['destinations'];
    }

    public function removeWarehouse($warehouse_id)
    {
        $this->db->query(
            'CREATE TEMPORARY TABLE _warehouse_affected_products'
            . ' (PRIMARY KEY product_id (product_id))'
            . ' ENGINE = MEMORY'
            . ' SELECT product_id FROM ?:warehouses_products_amount'
            . ' WHERE warehouse_id = ?i',
            $warehouse_id
        );

        $this->db->query('DELETE FROM ?:warehouses_products_amount WHERE ?w', [
            'warehouse_id' => $warehouse_id,
        ]);

        $this->db->query('DELETE FROM ?:store_location_destination_links WHERE ?w', [
            'store_location_id' => $warehouse_id,
        ]);

        $this->db->query('DELETE FROM ?:store_location_shipping_delays WHERE ?w', [
            'store_location_id' => $warehouse_id,
        ]);

        $this->recalculateDestinationProductsStocks([
            'by_temporary_table'     => '_warehouse_affected_products',
            'reset_stock_split_flag' => true,
        ]);

        $this->db->query('DROP TEMPORARY TABLE _warehouse_affected_products');

        /**
         * Executes after deleting warehouse data
         * Allows to delete related data
         *
         * @param int $warehouse_id Warehouse identifier
         */
        fn_set_hook('warehouses_manager_remove_warehouse', $warehouse_id);
    }

    /**
     * Recalculates the destination products amount.
     *
     * @param array<array-key, int> $product_ids Product IDs
     *
     * @return void
     */
    public function recalculateDestinationProductsStocksByProductIds(array $product_ids)
    {
        $product_ids = array_filter($product_ids);

        if (!$product_ids) {
            return;
        }

        $this->recalculateDestinationProductsStocks([
            'product_ids'            => $product_ids,
            'reset_stock_split_flag' => true,
        ]);
    }

    /**
     * Recalculate destination product stocks by warehouses.
     *
     * @param array $warehouse_ids Warehouses identifiers
     * @param array $params        Extra parameters
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function recalculateDestinationProductsStocksByWarehouseIds(array $warehouse_ids, array $params = [])
    {
        $warehouse_ids = array_filter($warehouse_ids);

        if (!$warehouse_ids) {
            return;
        }

        $this->recalculateDestinationProductsStocks([
            'warehouse_ids' => $warehouse_ids,
            'reset_stock_split_flag' => !empty($params['reset_stock_split_flag'])
        ]);
    }

    private function recalculateDestinationProductsStocks(array $params)
    {
        if (!empty($params['product_ids'])) {
            $product_condition = $this->db->quote('product_id IN (?n)', $params['product_ids']);
        } elseif (!empty($params['warehouse_ids'])) {
            $product_condition = $this->db->quote(
                'product_id IN (SELECT product_id FROM ?:warehouses_products_amount WHERE warehouse_id IN (?n) GROUP BY product_id)',
                $params['warehouse_ids']
            );
        } elseif (!empty($params['by_temporary_table'])) {
            $product_condition = $this->db->quote(
                'product_id IN (SELECT product_id FROM ?p)',
                $params['by_temporary_table']
            );
            $product_ids = $this->db->getColumn('SELECT product_id FROM ?p', $params['by_temporary_table']);
        }

        if (empty($product_condition)) {
            return false;
        }

        if (!empty($params['reset_stock_split_flag'])) {
            $this->db->query(
                'UPDATE ?:products SET is_stock_split_by_warehouses = ?s'
                . ' WHERE ?p AND is_stock_split_by_warehouses = ?s',
                YesNo::NO,
                $product_condition,
                YesNo::YES
            );
        }

        $this->db->query(
            'DELETE FROM ?:warehouses_destination_products_amount WHERE ?p',
            $product_condition
        );

        $this->db->query(
            'DELETE FROM ?:warehouses_sum_products_amount WHERE ?p',
            $product_condition
        );

        if ($this->is_mve) {
            $this->db->query(
                'INSERT INTO ?:warehouses_destination_products_amount (destination_id, storefront_id, product_id, amount)'
                . ' (SELECT destination_links.destination_id, 0 AS storefront_id, '
                .       ' warehouses_products_amount.product_id, SUM(warehouses_products_amount.amount) AS amount'
                . ' FROM ?:warehouses_products_amount AS warehouses_products_amount'
                . ' INNER JOIN ?:store_locations AS store_locations'
                .       ' ON store_locations.store_location_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:store_location_destination_links AS destination_links'
                .       ' ON destination_links.store_location_id = warehouses_products_amount.warehouse_id'
                . ' WHERE ?p AND store_locations.status = ?s'
                . ' GROUP BY destination_links.destination_id, warehouses_products_amount.product_id)',
                $product_condition,
                ObjectStatuses::ACTIVE
            );

            // Update summary amounts table for MVE
            $this->db->query(
                'INSERT INTO ?:warehouses_sum_products_amount (storefront_id, product_id, amount)'
                . ' (SELECT 0 AS storefront_id, warehouses_products_amount.product_id,'
                . ' SUM(warehouses_products_amount.amount) AS amount'
                . ' FROM ?:warehouses_products_amount AS warehouses_products_amount'
                . ' INNER JOIN ?:store_locations AS store_locations'
                .       ' ON store_locations.store_location_id = warehouses_products_amount.warehouse_id'
                . ' WHERE ?p AND store_locations.status = ?s'
                . ' GROUP BY warehouses_products_amount.product_id)',
                $product_condition,
                ObjectStatuses::ACTIVE
            );
        } else {
            $this->db->query(
                'INSERT INTO ?:warehouses_destination_products_amount (destination_id, storefront_id, product_id, amount)'
                . ' (SELECT destination_links.destination_id, storefronts_companies.storefront_id, '
                .       ' warehouses_products_amount.product_id, SUM(warehouses_products_amount.amount) AS amount'
                . ' FROM ?:warehouses_products_amount AS warehouses_products_amount'
                . ' INNER JOIN ?:store_locations AS store_locations'
                .       ' ON store_locations.store_location_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:store_location_destination_links AS destination_links'
                .       ' ON destination_links.store_location_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:ult_objects_sharing AS objects_sharing'
                .       ' ON objects_sharing.share_object_type = ?s'
                .           ' AND objects_sharing.share_object_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:storefronts_companies AS storefronts_companies'
                .       ' ON storefronts_companies.company_id = objects_sharing.share_company_id'
                . ' WHERE ?p AND store_locations.status = ?s'
                . ' GROUP BY destination_links.destination_id, storefronts_companies.storefront_id, '
                .   ' warehouses_products_amount.product_id)',
                'store_locations',
                $product_condition,
                ObjectStatuses::ACTIVE
            );

            // Update summary amounts table
            $this->db->query(
                'INSERT INTO ?:warehouses_sum_products_amount (storefront_id, product_id, amount)'
                . ' (SELECT storefronts_companies.storefront_id, warehouses_products_amount.product_id,'
                . ' SUM(warehouses_products_amount.amount) AS amount'
                . ' FROM ?:warehouses_products_amount AS warehouses_products_amount'
                . ' INNER JOIN ?:store_locations AS store_locations'
                .       ' ON store_locations.store_location_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:ult_objects_sharing AS objects_sharing'
                .       ' ON objects_sharing.share_object_type = ?s'
                .           ' AND objects_sharing.share_object_id = warehouses_products_amount.warehouse_id'
                . ' INNER JOIN ?:storefronts_companies AS storefronts_companies'
                .       ' ON storefronts_companies.company_id = objects_sharing.share_company_id'
                . ' WHERE ?p AND store_locations.status = ?s'
                . ' GROUP BY storefronts_companies.storefront_id, warehouses_products_amount.product_id)',
                'store_locations',
                $product_condition,
                ObjectStatuses::ACTIVE
            );

            // Update 'all storefronts' summary amounts
            $this->db->query(
                'INSERT INTO ?:warehouses_sum_products_amount (storefront_id, product_id, amount)'
                . ' (SELECT 0 as storefront_id, warehouses_sum_products_amount.product_id,'
                . ' SUM(warehouses_sum_products_amount.amount) AS amount'
                . ' FROM ?:warehouses_sum_products_amount AS warehouses_sum_products_amount'
                . ' WHERE ?p AND warehouses_sum_products_amount.storefront_id <> 0'
                . ' GROUP BY warehouses_sum_products_amount.product_id)',
                $product_condition
            );
        }

        /**
         * Executes after updating data about the availability of products in warehouses.
         *
         * @param \Tygh\Addons\Warehouses\Manager $this              Warehouses manager instance
         * @param array<array-key, string|bool>   $params            Recalculated parameters
         * @param string                          $product_condition Product condition
         */
        fn_set_hook('warehouses_recalculate_destination_products_stocks', $this, $params, $product_condition);

        if (!empty($params['reset_stock_split_flag'])) {
            $this->db->query(
                'UPDATE ?:products SET is_stock_split_by_warehouses = ?s'
                . ' WHERE product_id IN '
                . ' (SELECT product_id FROM ?:warehouses_products_amount'
                . ' LEFT JOIN ?:store_locations ON ?:warehouses_products_amount.warehouse_id = ?:store_locations.store_location_id'
                . ' WHERE ?p AND ?:store_locations.status = ?s'
                . ' GROUP BY product_id)',
                YesNo::YES,
                $product_condition,
                ObjectStatuses::ACTIVE
            );
        }
        if (isset($product_ids)) {
            foreach ($product_ids as $product_id) {
                $this->saveTotalAmount($product_id, $this->getProductWarehousesStock($product_id));
            }
        }

        return true;
    }

    /**
     * Removes product stocks info
     *
     * @param int $product_id Product ID
     */
    public function removeProductStocks(int $product_id): void
    {
        $this->db->query(
            'UPDATE ?:products SET is_stock_split_by_warehouses = ?s'
            . ' WHERE product_id = ?i AND is_stock_split_by_warehouses = ?s',
            YesNo::NO,
            $product_id,
            YesNo::YES
        );

        $this->db->query('DELETE FROM ?:warehouses_products_amount WHERE product_id = ?i', $product_id);
        $this->db->query('DELETE FROM ?:warehouses_destination_products_amount WHERE product_id = ?i', $product_id);
        $this->db->query('DELETE FROM ?:warehouses_sum_products_amount WHERE product_id = ?i', $product_id);

        /**
         * Executes after deleting product stocks.
         *
         * @param \Tygh\Addons\Warehouses\Manager $this       Warehouses manager instance
         * @param int                             $product_id Product ID
         */
        fn_set_hook('warehouses_remove_product_stocks_post', $this, $product_id);
    }

    /**
     * Saves total amount of specified product available in all warehouses combined.
     *
     * @param int                                  $product_id    Product identifier.
     * @param \Tygh\Addons\Warehouses\ProductStock $product_stock Product warehouse stock.
     * @param bool                                 $remove_all    Remove all records before save
     *
     * @return bool
     *
     * @throws \Tygh\Exceptions\DatabaseException Exception at replace operation.
     */
    private function saveTotalAmount($product_id, ProductStock $product_stock, $remove_all = true)
    {
        return $this->saveTotalAmounts([$product_id], [$product_stock], $remove_all);
    }

    /**
     * Saves total amount of specified products available in all warehouses combined.
     *
     * @param int[]                                  $product_ids    Product identifier.
     * @param \Tygh\Addons\Warehouses\ProductStock[] $product_stocks Product warehouse stock.
     * @param bool                                   $remove_all     Remove all records before save
     *
     * @return bool
     *
     * @throws \Tygh\Exceptions\DatabaseException Exception at replace operation.
     */
    private function saveTotalAmounts(array $product_ids, array $product_stocks, $remove_all = true)
    {
        $all_products_warehouses = db_get_fields(
            'SELECT DISTINCT warehouse_id FROM ?:store_locations AS sl'
            . ' INNER JOIN ?:warehouses_products_amount AS wpa ON wpa.warehouse_id = sl.store_location_id'
            . ' WHERE wpa.product_id IN (?n)',
            $product_ids
        );

        $warehouses_storefront_ids = [];
        $warehouses_sum_amounts = [];

        foreach ($all_products_warehouses as $warehouse_id) {
            $warehouses_storefront_ids[$warehouse_id] = $this->getStorefrontsByWarehouseId($warehouse_id);
        }

        foreach ($product_stocks as $product_stock) {
            foreach ($product_stock->getWarehouses() as $warehouse) {
                if ($warehouse->isMarkedToRemove()) {
                    continue;
                }

                if (!$warehouse->isActive()) {
                    continue;
                }

                $data = [];
                foreach ($warehouses_storefront_ids[$warehouse->getWarehouseId()] as $storefront_id) {
                    if (isset($data[$storefront_id])) {
                        $data[$storefront_id]['amount'] += $warehouse->getAmount();
                    } else {
                        $data[$storefront_id] = [
                            'product_id'    => $product_stock->getProductId(),
                            'amount'        => $warehouse->getAmount(),
                            'storefront_id' => $storefront_id
                        ];
                    }
                }

                foreach ($data as $storefront_data) {
                    $warehouses_sum_amounts[] = $storefront_data;
                }
            }
        }

        if (!$warehouses_sum_amounts && $remove_all) {
            return (bool) $this->db->query(
                'DELETE FROM ?:warehouses_sum_products_amount WHERE product_id IN (?n)',
                $product_ids
            );
        }

        $this->db->replaceInto('warehouses_sum_products_amount', $warehouses_sum_amounts, true);

        return true;
    }

    /**
     * Gets all storefronts on which specified warehouse shared.
     *
     * @param int $warehouse_id Warehouse identifier.
     *
     * @return int[]
     */
    private function getStorefrontsByWarehouseId($warehouse_id)
    {
        if ($this->is_mve) {
            return [0];
        }
        $company_ids = $this->db->getColumn(
            'SELECT share_company_id FROM ?:ult_objects_sharing WHERE share_object_type = ?s AND share_object_id = ?i',
            'store_locations',
            $warehouse_id
        );
        if (empty($company_ids)) {
            return [];
        }
        $repository = StorefrontProvider::getRepository();
        $result = [];
        foreach ($company_ids as $company_id) {
            /** @var \Tygh\Storefront\Storefront $storefront */
            $storefront = $repository->findByCompanyId($company_id);
            if (!$storefront) {
                continue;
            }
            $result[] = $storefront->storefront_id;
        }
        return $result;
    }
}
