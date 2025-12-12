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

defined('BOOTSTRAP') or die('Access denied');

if (isset(Tygh::$app['session']['license_information'])) {
    $license = Tygh::$app['session']['license_information'];

    if (strpos($license, '<?xml') !== false) {
        $license = simplexml_load_string($license);

        if (isset($license->OnlineTechSupportWidgetId)) {
            $tech_support_chat_widget_id = (string) $license->OnlineTechSupportWidgetId;
            fn_set_storage_data('tech_support_chat_widget_id', $tech_support_chat_widget_id);
        } else {
            fn_set_storage_data('tech_support_chat_widget_id');
        }
    }
}

Tygh::$app['view']->assign('tech_support_chat_widget_id', fn_get_storage_data('tech_support_chat_widget_id'));

return [CONTROLLER_STATUS_OK];
