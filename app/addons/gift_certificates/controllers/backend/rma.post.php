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

use \Tygh\Enum\Addons\Rma\ReturnOperationStatuses;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // Creation gift certificate
    //
    if ($mode == 'create_gift_certificate') {

        $change_return_status = $_REQUEST['change_return_status'];

        if (!empty($_REQUEST['accepted'])) {
            $total = 0;
            $return_info = fn_get_return_info($change_return_status['return_id']);
            foreach ((array) $_REQUEST['accepted'] as $item_id => $v) {
                if (isset($v['chosen']) && $v['chosen'] == 'Y') {
                    $total += $v['amount'] * $return_info['items'][ReturnOperationStatuses::APPROVED][$item_id]['price'];
                }
            }

            if ($total > 0) {
                $certificate = fn_create_return_gift_certificate($return_info['order_id'], fn_format_price($total));

                $return_info['extra'] = unserialize($return_info['extra']);

                if (!isset($return_info['extra']['gift_certificates'])) {
                    $return_info['extra']['gift_certificates'] = array();
                }

                $return_info['extra']['gift_certificates'] = fn_array_merge($return_info['extra']['gift_certificates'], $certificate);

                $_data = array('extra' => serialize($return_info['extra']));

                db_query("UPDATE ?:rma_returns SET ?u WHERE return_id = ?i", $_data, $change_return_status['return_id']);
            }
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'rma.details?return_id=' . $change_return_status['return_id']);
    }
}
