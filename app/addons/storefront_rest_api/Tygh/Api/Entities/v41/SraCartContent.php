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

namespace Tygh\Api\Entities\v41;

use Tygh\Api\Entities\v40\SraCartContent as SraCartContent40;

class SraCartContent extends SraCartContent40
{
    /**
     * Lists cart content.
     *
     * @param string $id     Cart ID
     * phpcs:ignore
     * @param array  $params Params
     *
     * phpcs:ignore
     * @return array{data: mixed, status: mixed}
     */
    //phpcs:ignore
    public function index($id = '', $params = [])
    {
        ['status' => $status, 'data' => $cart] = parent::index($id, $params);

        if (!empty($cart['chosen_shipping'])) {
            $chosen_shipping = [];

            foreach ((array) $cart['chosen_shipping'] as $product_group_index => $shipping_id) {
                $chosen_shipping[] = [
                    'product_group_index' => $product_group_index,
                    'shipping_id'         => $shipping_id,
                ];
            }

            $cart['chosen_shipping'] = $chosen_shipping;
        }

        return [
            'status' => $status,
            'data'   => $cart,
        ];
    }
}
