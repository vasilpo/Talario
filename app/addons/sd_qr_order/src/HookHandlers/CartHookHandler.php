<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\QrOrder\HookHandlers;

use Tygh\Addons\QrOrder\Helpers\QrHelper;
use Tygh\Tygh;
use Tygh\Registry;
use Tygh\Storage;

class CartHookHandler
{
    /**
     *  The "change_order_status" hook handler.
     *  Actions performed:
     *      -Generates a QR code for ordering.
     *
     * @param string $status_to
     * @param string $status_from
     * @param array $order_info
     * @param bool $force_notification
     * @param array $order_statuses
     * @param string $place_order
     *
     * @see fn_change_order_status()
     */
    public function changeOrderStatus($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
    {
        {
            $selected_statuses = (array) Registry::get('addons.sd_qr_order.statuses');
            if (array_key_exists($status_to, $selected_statuses)) {
                QrHelper::generateOrderQr($order_info['order_id']);
            }
        }
    }

    /**
     *  The "delete_order" hook handler.
     *  Actions performed:
     *          -Deleting the datamatrix along with the order.
     *
     * @param int $order_id Order ID
     *
     * @see fn_delete_order()
     */
    public function deleteOrder($order_id)
    {
        if (!empty($order_id)) {
            Storage::instance('images')->deleteByPattern('qr_code_orders' . '/' . $order_id . '/');
        }
    }
}
