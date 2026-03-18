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
