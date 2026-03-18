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

/** @var string $mode Dispatch mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $object_id = isset($_REQUEST['object_id'])
        ? $_REQUEST['object_id']
        : null;

    $object_type = isset($_REQUEST['object_type'])
        ? $_REQUEST['object_type']
        : null;

    if (!$object_id || $object_type !== 'product' || !fn_get_runtime_company_id()) {
        return [CONTROLLER_STATUS_OK];
    }

    $company_data = Registry::get('runtime.company_data');

    $is_premoderation_required = false;

    if ($mode === 'add') {
        if (fn_vendor_data_premoderation_product_requires_approval($company_data, true)) {
            $is_premoderation_required = fn_vendor_data_premoderation_request_approval_for_products([$object_id], true, true);
        }
    }

    if ($mode === 'update' || $mode === 'delete') {
        $current_status = fn_vendor_data_premoderation_get_current_product_statuses([$object_id])[$object_id];
        if (fn_vendor_data_premoderation_product_requires_approval($company_data, false, $current_status)) {
            $is_premoderation_required = fn_vendor_data_premoderation_request_approval_for_products([$object_id], true, true);
        }
    }

    if ($is_premoderation_required) {
        fn_set_notification(
            NotificationSeverity::WARNING,
            __('warning'),
            __('vendor_data_premoderation.products_sent_to_premoderation', [
                1,
                '[product_approval_url]' => fn_url(Url::buildUrn(['products', 'manage'], [
                    'status' => ProductStatuses::REQUIRES_APPROVAL,
                ])),
            ])
        );
    }
}
