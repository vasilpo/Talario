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

namespace Tygh\Addons\TinkoffMultiparty\HookHandlers;

use Tygh\Addons\TinkoffMultiparty\Enum\PaymentSessionStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Tygh;

class PaymentsHookHandler
{
    /**
     * The `get_payment_processors_post` hook handler.
     *
     * Actions performed:
     *     - Adds specific attributes to some payment processor for categorization.
     *
     * @param string                            $lang_code  Language code.
     * @param array<array<string, string|bool>> $processors Payment processors.
     *
     * @see \fn_get_payment_processors()
     *
     * @return void
     */
    public function onGetPaymentProcessorsPost($lang_code, array &$processors)
    {
        foreach ($processors as &$processor) {
            if ($processor['addon'] !== 'tinkoff_multiparty') {
                continue;
            }
            $processor['russian'] = true;
        }
        unset($processor);
    }

    /**
     * The "get_payments" hook handler.
     *
     * Actions performed:
     *     - Excludes T-Bank Multiparty from payments selection when products vendor has no T-Bank Multiparty ShopCode
     *
     * @param array<string, string|int> $params    Array of flags/data which determines which data should be gathered
     * @param array<string>             $fields    List of fields for retrieving
     * @param array<string>             $join      Array with the complete JOIN information (JOIN type, tables and fields) for an SQL-query
     * @param array<string>             $order     Array containing SQL-query with sorting fields
     * @param array<string>             $condition Array containing SQL-query condition possibly prepended with a logical operator AND
     * @param array<string>             $having    Array containing SQL-query condition to HAVING group
     *
     * @return void
     *
     * @see \fn_get_payments()
     */
    public function onGetPayments(array $params, array $fields, array $join, array $order, array &$condition, array $having)
    {
        if (
            $params['area'] !== SiteArea::STOREFRONT
            && !defined('ORDER_MANAGEMENT')
            || empty(Tygh::$app['session']['cart']['product_groups'])
        ) {
            return;
        }

        foreach (Tygh::$app['session']['cart']['product_groups'] as $product_group) {
            if (!$product_group['company_id']) {
                continue;
            }

            $company_data = fn_get_company_data($product_group['company_id']);
            if ($company_data['tinkoff_multiparty_shopcode']) {
                continue;
            }

            $condition[] = db_quote(
                '(?:payment_processors.processor_script IS NULL'
                . ' OR ?:payment_processors.processor_script <> ?s)',
                'tinkoff_multiparty.php'
            );

            return;
        }
    }

    /**
     * The "prepare_checkout_payment_methods_after_get_payments" hook handler.
     *
     * Actions performed:
     *  - Excludes T-Bank Multiparty from payments selection if vendor has no T-Bank Multiparty ShopCode
     *  - Excludes T-Bank Multiparty from payments selection on repay page if the payment status of the order is set to "REVERSED"
     *
     * @param array<string, string|int|array<string, int|string>> $cart                Array of the cart contents and user information necessary for purchase
     * @param array<string, string>                               $auth                Array of user authentication data (e.g. uid, usergroup_ids, etc.)
     * @param string                                              $lang_code           Two-letter language code (e.g. 'en', 'ru', etc.)
     * @param bool                                                $get_payment_groups  If set to true, payment methods groupped by category will be returned
     * @param array<string, array<string, array<string, string>>> $payment_methods     Payment methods
     * @param array<string, array<string, string|int>|int|string> $get_payments_params Parameters that are used to fetch payments
     * @param string                                              $cache_key           The unique key that is used to cache fetched payments
     *
     * @return void
     *
     * @see \fn_prepare_checkout_payment_methods()
     */
    public function onAfterGetPayments(
        array $cart,
        array $auth,
        $lang_code,
        $get_payment_groups,
        array &$payment_methods,
        array $get_payments_params,
        $cache_key
    ) {
        if (empty($payment_methods[$cache_key])) {
            return;
        }

        $exclude_from_repay = false;
        if (!empty($cart['order_id'])) {
            $order_info = fn_get_order_info((int) $cart['order_id']);

            if (
                !empty($order_info['payment_method']['processor'])
                && $order_info['payment_method']['processor'] === 'T-Bank Multiparty'
                && !empty($order_info['payment_info']['addons.tinkoff_multiparty.payment_status'])
                && $order_info['payment_info']['addons.tinkoff_multiparty.payment_status'] === PaymentSessionStatuses::REVERSED
            ) {
                $exclude_from_repay = true;
            }
        }

        if (empty($get_payments_params['company_id']) && empty($order_info['company_id'])) {
            return;
        }

        $company_id = !empty($get_payments_params['company_id']) ? $get_payments_params['company_id'] : $order_info['company_id'];
        $company_data = fn_get_company_data((int) $company_id);
        if ($company_data['tinkoff_multiparty_shopcode'] && !$exclude_from_repay) {
            return;
        }

        foreach ($payment_methods[$cache_key] as $payment_id => $payment_method) {
            if ($payment_method['processor_script'] !== 'tinkoff_multiparty.php') {
                continue;
            }
            unset($payment_methods[$cache_key][$payment_id]);
        }
    }
}
