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

use Tygh\Addons\YandexCheckout\Enum\ProcessorScript;
use Tygh\Addons\YandexCheckout\Enum\SystemTaxCode;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    || !fn_allowed_for('MULTIVENDOR')
) {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    $yandex_checkout_for_marketplaces_payment_methods = fn_get_payments(
        [
            'processor_script' => ProcessorScript::YANDEX_CHECKOUT_FOR_MARKETPLACES,
            'status' => ObjectStatuses::ACTIVE,
        ]
    );
    $is_vendor_plans_installed = Registry::get('addons.vendor_plans.status') === ObjectStatuses::ACTIVE;
    $view->assign([
        'is_yandex_checkout_for_marketplaces_used' => !empty($yandex_checkout_for_marketplaces_payment_methods),
        'is_vendor_plans_installed' => (int) $is_vendor_plans_installed,
        'addons_page' => fn_url('addons.update?addon=vendor_plans'),
        'yandex_tax_codes' => SystemTaxCode::getAll()
    ]);
}

return [CONTROLLER_STATUS_OK];
