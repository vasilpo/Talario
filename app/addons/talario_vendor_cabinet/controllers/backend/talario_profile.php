<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $company_data = fn_get_company_data($company_id, DESCR_SL, ['skip_cache' => true]);
    $profile_fields = fn_get_profile_fields('C');

    Tygh::$app['view']->assign([
        'talario_company' => $company_data,
        'profile_fields' => $profile_fields,
        'countries' => fn_get_simple_countries(true, CART_LANGUAGE),
        'states' => fn_get_all_states(),
    ]);
}
