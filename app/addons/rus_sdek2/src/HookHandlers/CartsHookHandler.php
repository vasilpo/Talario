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

namespace Tygh\Addons\RusSdek2\HookHandlers;

use Tygh\Application;

/**
 * This class describes the hook handlers related to cart, checkout, and order management
 *
 * @package Tygh\Addons\RusSdek2\HookHandlers
 */
class CartsHookHandler
{
    /** @var Application */
    protected $application;

    /**
     * @param Application $application Application object
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * The `update_cart_by_data_post` hook handler.
     *
     * Action performed:
     *     - Adds necessary data to shipping.
     *
     * @param array $cart          Cart content
     * @param array $new_cart_data New cart data
     * @param array $auth          Auth data
     *
     * @see \fn_calculate_cart_content()
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onUpdateCartByDataPost(array &$cart, array $new_cart_data, array $auth)
    {
        if (empty($new_cart_data['select_office'])) {
            return;
        }

        $cart['select_office'] = $new_cart_data['select_office'];
    }

    /**
     * The `calculate_cart_taxes_pre` hook handler.
     *
     * Action performed:
     *     - Adds necessary shipping data to cart.
     *
     * @param array $cart           Cart content
     * @param array $cart_products  Cart products data
     * @param array $product_groups Product groups data
     *
     * @see \fn_calculate_cart_content()
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onCalculateCartTaxesPre(array &$cart, array $cart_products, array &$product_groups)
    {
        if (!empty($cart['shippings_extra']['data'])) {
            if (!empty($cart['select_office'])) {
                $select_office = $cart['select_office'];
            } elseif (!empty($_REQUEST['select_office'])) {
                $select_office = $cart['select_office'] = $_REQUEST['select_office'];
            }

            if (!empty($select_office)) {
                foreach ($product_groups as $group_key => $group) {
                    if (!empty($group['chosen_shippings'])) {
                        foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                            $shipping_id = $shipping['shipping_id'];

                            if (
                                $shipping['module'] !== 'sdek2'
                                || empty($cart['shippings_extra']['data'][$group_key][$shipping_id])
                            ) {
                                continue;
                            }

                            $shippings_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];
                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shippings_extra;

                            if (empty($select_office[$group_key][$shipping_id])) {
                                continue;
                            }

                            $office_id = $select_office[$group_key][$shipping_id];
                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['office_id'] = $office_id;

                            if (empty($shippings_extra['offices'][$office_id])) {
                                continue;
                            }

                            $office_data = $shippings_extra['offices'][$office_id];
                            $product_groups[$group_key]['chosen_shippings'][$shipping_key]['office_data'] = $office_data;
                        }
                    }
                }
            }

            if (!empty($cart['shippings_extra']['data'])) {
                foreach ($cart['shippings_extra']['data'] as $group_key => $shippings) {
                    foreach ($shippings as $shipping_id => $shippings_extra) {
                        if (!empty($product_groups[$group_key]['shippings'][$shipping_id]['module'])) {
                            $module = $product_groups[$group_key]['shippings'][$shipping_id]['module'];

                            if ($module === 'sdek2' && !empty($shippings_extra)) {
                                $product_groups[$group_key]['shippings'][$shipping_id]['data'] = $shippings_extra;

                                if (!empty($shippings_extra['delivery_time'])) {
                                    $product_groups[$group_key]['shippings'][$shipping_id]['delivery_time'] = $shippings_extra['delivery_time'];
                                }
                            }
                        }
                    }
                }
            }

            foreach ($product_groups as $group_key => $group) {
                if (!empty($group['chosen_shippings'])) {
                    foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                        $shipping_id = $shipping['shipping_id'];
                        $module = $shipping['module'];

                        if ($module !== 'sdek2' || empty($cart['shippings_extra']['data'][$group_key][$shipping_id])) {
                            continue;
                        }

                        $shipping_extra = $cart['shippings_extra']['data'][$group_key][$shipping_id];
                        $product_groups[$group_key]['chosen_shippings'][$shipping_key]['data'] = $shipping_extra;
                    }
                }
            }
        }
    }

    /**
     * The "place_suborders_pre" hook handler.
     *
     * Actions performed:
     * - Generates the correct keys of "shippings_extra" and "select_office" in suborder cart.
     *
     * @param int                                                                                                                         $order_id      Order identifier
     * @param array<string|int>                                                                                                           $cart          Cart contents
     * @param array<string|int>                                                                                                           $auth          Authentication data
     * @param string                                                                                                                      $action        Current action. Can be empty or "save"
     * @param int                                                                                                                         $issuer_id     Issuer identifier
     * @param array{shippings_extra?: array{data?: array<string|int>}, chosen_shipping?: array<int>, shipping?: array<array<string|int>>} $suborder_cart Child cart contents
     * @param int                                                                                                                         $key_group     Child cart products group key
     * @param array<string|int>                                                                                                           $group         Child cart products
     *
     * @see \fn_place_suborders()
     *
     * @return void
     */
    public function onPlaceSubordersPre($order_id, $cart, $auth, $action, $issuer_id, &$suborder_cart, $key_group, $group)
    {
        if (
            !isset($suborder_cart['chosen_shipping'][$key_group])
            || !isset($suborder_cart['shipping'][$suborder_cart['chosen_shipping'][$key_group]])
        ) {
            return;
        }

        $shipping = $suborder_cart['shipping'][$suborder_cart['chosen_shipping'][$key_group]];

        if (
            empty($shipping['module'])
            || $shipping['module'] !== 'sdek2'
        ) {
            return;
        }

        if (isset($suborder_cart['shippings_extra']['data'][$key_group])) {
            $suborder_cart['shippings_extra']['data'] = [$suborder_cart['shippings_extra']['data'][$key_group]];
        }

        if (!isset($suborder_cart['select_office'][$key_group])) {
            return;
        }

        $suborder_cart['select_office'] = [$suborder_cart['select_office'][$key_group]];
    }

    /**
     * The "pre_update_order" hook handler.
     *
     * Actions performed:
     *     - Deletes information about available pick-up points in cart.
     *
     * @param array<string|int|float|array<string|int|float>> $cart     Cart contents
     * @param int                                             $order_id Order identifier
     *
     * @psalm-param array{
     *   product_groups?: array<
     *     int, array{
     *       chosen_shippings?: array<
     *         int, array{
     *           shipping_id: int,
     *           module?: string,
     *           data?: array{
     *              stores: array<
     *                int, array{
     *                  company_id: int
     *                }
     *              >
     *           }
     *         }
     *       >,
     *       shippings?: array<
     *         int, array{
     *           shipping_id: int,
     *           module?: string,
     *           data?: array{
     *              stores: array<
     *                int, array{
     *                  company_id: int
     *                }
     *              >
     *           }
     *         }
     *       >
     *     }
     *   >
     * } $cart
     *
     * @see \fn_update_order()
     *
     * @return void
     */
    public function onPreUpdateOrder(array &$cart, $order_id)
    {
        if (empty($cart['product_groups'])) {
            return;
        }

        foreach ($cart['product_groups'] as $group_id => $group) {
            $group_shipping = !empty($group['chosen_shippings']) ? $group['chosen_shippings'] : [];

            foreach ($group_shipping as $shipping_id => $shipping) {
                if (
                    empty($shipping['data'])
                    || empty($shipping['module']) || $shipping['module'] !== 'sdek2'
                    || empty($shipping['office_id'])
                ) {
                    continue;
                }

                /** @psalm-suppress InvalidArrayOffset */
                unset($cart['product_groups'][$group_id]['chosen_shippings'][$shipping_id]['data']['offices']);
                /** @psalm-suppress InvalidArrayOffset */
                unset($cart['product_groups'][$group_id]['chosen_shippings'][$shipping_id]['data']['sdek_offices']);

                if (
                    empty($shipping['shipping_id'])
                    || empty($cart['product_groups'][$group_id]['shippings'][$shipping['shipping_id']]['data'])
                ) {
                    continue;
                }

                /** @psalm-suppress InvalidArrayOffset */
                unset($cart['product_groups'][$group_id]['shippings'][$shipping['shipping_id']]['data']['offices']);
                /** @psalm-suppress InvalidArrayOffset */
                unset($cart['product_groups'][$group_id]['shippings'][$shipping['shipping_id']]['data']['sdek_offices']);
            }
        }
    }
}
