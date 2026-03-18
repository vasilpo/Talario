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

use Tygh\Providers\LicensingProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param string $feature Value of \Tygh\Licensing\Features
 *
 * @return bool
 */
function fn_upsell_is_upsellable($feature)
{
    if (ACCOUNT_TYPE !== 'admin' || !Registry::get('settings.Upgrade_center.license_number')) {
        return false;
    }

    $feature = fn_strtolower($feature);

    $upsell_features = fn_get_schema('upsell', 'upsell_features');

    if (!in_array($feature, $upsell_features)) {
        return false;
    }

    $current_plan = LicensingProvider::getLicensingService()->getCurrentPlan();

    return isset($current_plan->getFeatureCollection()[$feature]) && !fn_is_allowed($feature);
}
