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

use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;
use Tygh\Tools\Url;

defined('BOOTSTRAP') or die('Access denied');

$modes = ['update', 'm_update', 'update_file', 'update_folder', 'delete_file', 'delete_folder'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($mode, $modes, true) && fn_get_runtime_company_id()) {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    /** @var int $product_id */
    $product_ids = [];
    $modes = ['update_file', 'update_folder', 'delete_file', 'delete_folder'];
    if ($mode === 'update') {
        $product_ids = [$view->getTemplateVars('product_id')];
    } elseif (isset($_REQUEST['products_data'])) {
        $product_ids = array_keys($_REQUEST['products_data']);
    } elseif (in_array($mode, $modes, true)) {
        $product_ids = [$_REQUEST['product_id']];
    }
    $product_ids = array_filter($product_ids);

    if ($product_ids) {
        $current_statuses = fn_vendor_data_premoderation_get_current_product_statuses($product_ids);
        $pending_products = array_filter($current_statuses, function($status) {
            return $status === ProductStatuses::REQUIRES_APPROVAL;
        });

        if ($pending_products) {
            fn_set_notification(
                NotificationSeverity::WARNING,
                __('warning'),
                __('vendor_data_premoderation.products_sent_to_premoderation', [
                    count($pending_products),
                    '[product_approval_url]' => fn_url(Url::buildUrn(['products', 'manage'], [
                        'status' => ProductStatuses::REQUIRES_APPROVAL,
                    ]))
                ])
            );
        }
    }
}

if ($mode === 'manage' || $mode === 'master_products') {
    $status = isset($_REQUEST['status'])
        ? (array) $_REQUEST['status']
        : [];

    if (count($status) === 1) {
        $status = reset($status);
    } else {
        $status = null;
    }
}

return [CONTROLLER_STATUS_OK];
