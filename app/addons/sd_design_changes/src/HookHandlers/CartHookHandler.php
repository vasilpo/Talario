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

use Tygh\Tygh;

class CartHookHandler
{
    /**
     * The 'get_cart_product_data' hook handler
     * Executes after getting product data from database.
     *
     * @param int   $product_id Product ID
     * @param array $_pdata     Product data
     * @param array $product    Product data
     * @param array $auth       Array with authorization data
     * @param array $cart       Array of cart content and user information necessary for purchase
     * @param int   $hash       Unique product HASH
     * @return void
     *
     * @see \fn_get_cart_product_data()
     */
    public static function getCartProductData($product_id, $_pdata, &$product, $auth, $cart, $hash): void
    {
        if (!empty($product['extra']['booking_info']) && !empty($_pdata['address'])) {
            $product['extra']['booking_info']['address'] = trim($_pdata['address']);
        }
    }

    /**
     * The 'get_order_items_info_post' hook handler
     *
     * @param array $order
     * @param array $product
     * @param string $item_id
     * @return void
     *
     * @see \fn_get_order_info()
     */
    public static function getOrderItemsInfoPost(&$order, $product, $item_id): void
    {
        if (!empty($order['products'][$item_id]['extra']['booking_info'])
            && !empty($product['product_id'])
            && ($address = Tygh::$app['db']->getField('SELECT address FROM ?:product_descriptions WHERE ?w', [
                'product_id' => $product['product_id'],
                'lang_code' => $order['lang_code'] ?: CART_LANGUAGE,
            ]))
        ) {
            $order['products'][$item_id]['extra']['booking_info']['address'] = trim($address);
        }
    }

    /**
     * The 'pre_get_cart_product_data' hook handler
     * Prepare params before getting product data from cart
     *
     * @param string                           $hash             Unique product HASH
     * @param array<string, int|string|array>  $product          Product data
     * @param bool                             $skip_promotion   Skip promotion calculation
     * @param array<string, int|string|array>  $cart             Array of cart content and user information necessary for purchase
     * @param array<string, int|string|array>  $auth             Array with authorization data
     * @param int                              $promotion_amount Amount of product in promotion (like Free products, etc)
     * @param array<string, string>            $fields           SQL query fields
     * @param string                           $join             JOIN statement
     * @param array<string, array>             $params           Array of additional params
     * @return void
     *
     * @see \fn_get_cart_product_data()
     */
    public static function getCartProductDataPre($hash, $product, $skip_promotion, $cart, $auth, $promotion_amount, &$fields, $join, $params): void
    {
        $fields['address'] = '?:product_descriptions.address';
    }
}
