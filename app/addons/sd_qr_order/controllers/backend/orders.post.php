<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

use Tygh\Addons\QrOrder\Helpers\QrHelper;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'details') {
    if (!empty($_REQUEST['order_id'])) {
        $order_id = (int) $_REQUEST['order_id'];
        $qr_url = QrHelper::getOrderQr($order_id);
        if ($qr_url) {
            Tygh::$app['view']->assign('qr_code_url', $qr_url);
        }
    }
}
