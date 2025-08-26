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

namespace Tygh\Addons\RusSdek2\Services;

use Tygh\Addons\RusSdek2\Shippings\SdekApiClient;
use Tygh\Database\Connection;
use Tygh\Enum\NotificationSeverity;
use Tygh\Navigation\LastView;
use Tygh\Registry;
use Tygh\Shippings\Shippings;

class SdekService
{
    /** @var Connection */
    protected $db;

    /**
     * @param Connection $db Database
     *
     * @return void
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Checks if the data about shipment statuses exists.
     *
     * @return bool Returns true if the data about shipment statuses exists; returns false otherwise
     */
    public function deliveryCheckOrders()
    {
        $status_id = $this->db->getField('SELECT status_id FROM ?:rus_sdek2_history_status LIMIT 1');

        return !empty($status_id);
    }



    /**
     * Gets the data of specific shipment statuses; the passed parameters determine,
     * the data of which statuses will be returned.
     *
     * @param array $params         The parameters by which the shipment statuses will be selected
     * @param int   $items_per_page The maximum number of statuses to appear on one page
     *
     * @return array Returns an array of shipment statuses, and the parameters of the selected
     *        statuses
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getStatus(array $params = [], $items_per_page = 0)
    {
        $condition = 'WHERE 1 ';
        $_view = 'sdek_status';
        $params = LastView::instance()->update($_view, $params);

        $default_params = [
            'page' => 1,
            'items_per_page' => $items_per_page
        ];

        $params = array_merge($default_params, $params);

        if (!empty($params['time_from'])) {
            $condition .= $this->db->quote(' AND timestamp >= ?i', strtotime($params['time_from']));

            if (!empty($params['time_to'])) {
                $condition .= $this->db->quote(' AND timestamp < ?i', strtotime($params['time_to']));
            }
        }

        if (!empty($params['sdek_order_id'])) {
            $condition .= $this->db->quote(' AND order_id = ?i ', $params['sdek_order_id']);
        }

        $sort_by = !empty($params['sort_by']) ? $params['sort_by'] : 'order_id';
        $sort = 'asc';
        if (!empty($params['sort_order'])) {
            $sort = $params['sort_order'];
            $params['sort_order'] = ($params['sort_order'] === 'asc') ? 'desc' : 'asc';
            $params['sort_order_rev'] = $params['sort_order'];
        } else {
            $params['sort_order'] = 'asc';
            $params['sort_order_rev'] = 'asc';
        }

        $limit = '';
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = $this->db->getField('SELECT COUNT(*) FROM ?:rus_sdek2_history_status ?p', $condition);
            $limit = db_paginate($params['page'], $params['items_per_page']);
        }

        $data_status = $this->db->getArray(
            'SELECT * FROM ?:rus_sdek2_history_status ?p ORDER BY ?p ?p ?p',
            $condition,
            $sort_by,
            $sort,
            $limit
        );

        return [$data_status, $params];
    }

    /**
     * Sets the weight of a product/order to 100 grams in the specified weight measurement unit
     * when the product/order doesn't have weight.
     *
     * @param float|int $weight       The weight of a product or order.
     * @param float|int $symbol_grams Grams in the unit of weight defined by the weight symbol.
     *
     * @return float|int The weight of a product or order.
     */
    public function checkWeight($weight, $symbol_grams)
    {
        //phpcs:ignore
        if (empty($weight) || $weight == 0) {
            $weight = (100 / $symbol_grams);
        }

        return $weight;
    }

    /**
     * Calculates shipping costs for SDEK.
     *
     * @param array $order_info    The array with the order data
     * @param array $shipping_info The array with the shipping method data
     * @param array $shipment_info The array with the shipment data
     *
     * @return int
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function calculateCostByShipment(array $order_info, array $shipping_info, array $shipment_info)
    {
        $length = $width = $height = SDEK2_DEFAULT_DIMENSIONS;

        $shipping_info['module'] = $shipment_info['carrier'];

        $symbol_grams = Registry::get('settings.General.weight_symbol_grams');

        $products = [];

        foreach ($shipment_info['products'] as $item_id => $amount) {
            $product = $order_info['products'][$item_id];

            $product_extra = $this->db->getRow('SELECT shipping_params, weight FROM ?:products WHERE product_id = ?i', $product['product_id']);

            $product_weight = $this->checkWeight($product_extra['weight'], $symbol_grams);

            $p_ship_params = unserialize($product_extra['shipping_params']);

            $package_length = empty($p_ship_params['box_length']) ? $length : $p_ship_params['box_length'];
            $package_width = empty($p_ship_params['box_width']) ? $width : $p_ship_params['box_width'];
            $package_height = empty($p_ship_params['box_height']) ? $height : $p_ship_params['box_height'];
            $weight_ar = fn_convert_weight_to_imperial_units($product_weight);
            $weight = $weight_ar['plain'];

            $params_product = [
                'weight' => $weight,
                'length' => $package_length,
                'width' => $package_width,
                'height' => $package_height
            ];

            foreach ($order_info['product_groups'] as $product_groups) {
                if (!empty($product_groups['products'][$item_id])) {
                    $products[$item_id] = $product_groups['products'][$item_id];
                    $products[$item_id] = array_merge($products[$item_id], $params_product);
                    $products[$item_id]['amount'] = $amount;
                }

                $shipping_info['package_info'] = $product_groups['package_info'];
            }
        }

        $data_package = Shippings::groupProductsList($products, $shipping_info['package_info']['location']);
        $data_package = reset($data_package);
        $shipping_info['package_info_full'] = $data_package['package_info_full'];
        $shipping_info['package_info'] = $data_package['package_info_full'];

        $sum_rate = Shippings::calculateRates([$shipping_info]);
        $sum_rate = reset($sum_rate);

        return $sum_rate['price'];
    }

    /**
     * Gets order status from SDEK.
     *
     * @param array $shipping_params Shipping params
     * @param int   $order_id        Order ID
     * @param int   $shipment_id     Shipment ID
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function orderStatus(array $shipping_params, $order_id = 0, $shipment_id = 0)
    {
        if (empty($shipment_id)) {
            return [];
        }

        [$shipments,] = fn_get_shipments_info(['order_id' => $order_id]);
        $shipment = reset($shipments);

        $data_status = [];

        $order_uuid = $this->getSdekOrderUUIDByTrackingNumber($shipment['tracking_number']);

        if (!empty($order_uuid)) {
            try {
                $client_sdek = new SdekApiClient($shipping_params);

                $response = $client_sdek->getOrder($order_uuid);

                if (!empty($response)) {
                    $order = explode('_', $response->number);

                    $order_shipment_id = isset($order[1])
                        ? (int) $order[1]
                        : null;

                    if ($order_shipment_id) {
                        foreach ($response->statuses as $status) {
                            $data_status[$order[0] . '_' . $order[1] . '_' . $status->code] = [
                                'status_id'   => $status->code,
                                'order_id'    => $order[0],
                                'shipment_id' => $order[1],
                                'timestamp'   => strtotime($status->date_time),
                                'status'      => $status->name,
                                'city'        => $status->city,
                                // phpcs:ignore
                                'date'        => date("d-m-Y", strtotime($status->date_time)),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
            }
        }

        return $data_status;
    }

    /**
     * Update SDEK order statuses.
     *
     * @param array $date_status Status data
     *
     * @return void|bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function addStatusOrders(array $date_status)
    {
        if (empty($date_status)) {
            return false;
        }

        $sdek_history = [];
        $_data_status = $this->db->getArray('SELECT * FROM ?:rus_sdek2_status');
        foreach ($_data_status as $_status) {
            $id_history = $_status['order_id'] . '_' . $_status['shipment_id'];

            $n_status = [
                'status_id' => $_status['status_id'],
                'order_id' => $_status['order_id'],
                'shipment_id' => $_status['shipment_id'],
                'timestamp' => $_status['timestamp'],
                'status' => $_status['status'],
                'city' => $_status['city'],
                'id' => $_status['id']
            ];

            if (!empty($sdek_history[$id_history])) {
                if ($sdek_history[$id_history]['timestamp'] < $_status['timestamp']) {
                    $sdek_history[$id_history] = $n_status;
                }
            } else {
                $sdek_history[$id_history] = $n_status;
            }
        }

        $new_statuses = [];
        foreach ($date_status as $d_status) {
            $status_id = $this->db->getRow(
                'SELECT id FROM ?:rus_sdek2_status'
                . ' WHERE status_id = ?i and order_id = ?i and shipment_id = ?i ',
                $d_status['status_id'],
                $d_status['order_id'],
                $d_status['shipment_id']
            );

            $n_status = [
                'status_id' => $d_status['status_id'],
                'order_id' => $d_status['order_id'],
                'shipment_id' => $d_status['shipment_id'],
                'timestamp' => $d_status['timestamp'],
                'status' => $d_status['status'],
                'city' => $d_status['city']
            ];

            if (empty($status_id)) {
                $new_statuses[] = $n_status;
            }

            $n_status['id'] = empty($d_status['id']) ? '' : $d_status['status_id'];

            $id_history = $n_status['order_id'] . '_' . $n_status['shipment_id'];

            if (!empty($sdek_history[$id_history])) {
                if ($sdek_history[$id_history]['timestamp'] < $n_status['timestamp']) {
                    $sdek_history[$id_history] = $n_status;
                }
            } else {
                $sdek_history[$id_history] = $n_status;
            }
        }

        if (!empty($new_statuses)) {
            $this->db->query('INSERT INTO ?:rus_sdek2_status ?m', $new_statuses);
        }

        $d_status_ids = $this->db->getMultiHash(
            'SELECT order_id, shipment_id, id'
            . ' FROM ?:rus_sdek2_history_status',
            ['order_id', 'shipment_id']
        );

        foreach ($sdek_history as $k_history => $_history) {
            $sdek_history[$k_history]['id'] = '';

            if (!empty($d_status_ids[$_history['order_id']][$_history['shipment_id']]['id'])) {
                $sdek_history[$k_history]['id'] = $d_status_ids[$_history['order_id']][$_history['shipment_id']]['id'];
            }
        }

        if (!empty($sdek_history)) {
            $this->db->query('REPLACE INTO ?:rus_sdek2_history_status ?m', $sdek_history);
        }
    }

    /**
     * Filters SDEK postamat by checking product physical parameters with postamat parameters.
     *
     * @param array<string, array<string, array<string, string>>> $offices          SDEK postamat at destination.
     * @param array<array<string, float|int|string>>              $goods_parameters Physical parameters of product.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public function filterOffices(array $offices, array $goods_parameters)
    {
        foreach ($goods_parameters as $goods_parameter) {
            $offices = array_filter($offices, static function ($office) use ($goods_parameter) {
                if (
                    empty($office['weight_limit'])
                    || (
                        $goods_parameter['weight'] < $office['weight_limit']['WeightMin']
                        || $goods_parameter['weight'] > $office['weight_limit']['WeightMax']
                    )
                ) {
                    return false;
                }
                if (
                    empty($office['dimensions'])
                    || (
                        $goods_parameter['length'] > $office['dimensions']['depth']
                        || $goods_parameter['height'] > $office['dimensions']['height']
                        || $goods_parameter['width'] > $office['dimensions']['width']
                    )
                ) {
                    return false;
                }
                return true;
            });
        }
        return $offices;
    }

    /**
     * Gets SDEK order UUID by tracking number.
     *
     * @param string $tracking_number The weight of a product or order
     *
     * @return string
     */
    public function getSdekOrderUUIDByTrackingNumber($tracking_number)
    {
        return $this->db->getField('SELECT sdek_order_uuid FROM ?:rus_sdek2_register WHERE sdek_number = ?s', $tracking_number);
    }
}
