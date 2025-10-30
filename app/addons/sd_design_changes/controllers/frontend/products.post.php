<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

use Tygh\Enum\ProfileFieldLocations;
use Tygh\Enum\ProfileTypes;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'view' || $mode == 'quick_view') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    $product = $view->getTemplateVars('product');
    if (!empty($product['company_id'])) {
        $view->assign('company_data', fn_get_company_data($product['company_id']));

        if ($field_id = Registry::get('addons.sd_design_changes.metro_profile_field_id')) {
            $view->assign('company_profile_fields', fn_get_profile_fields(ProfileFieldLocations::ADMIN_FIELDS, [], CART_LANGUAGE, [
                'get_custom' => true,
                'profile_type' => ProfileTypes::CODE_SELLER,
                'skip_email_field' => false,
            ]));
        }
    }
}

return [CONTROLLER_STATUS_OK];
