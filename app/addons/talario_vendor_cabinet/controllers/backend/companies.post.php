<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

if (
    $_SERVER['REQUEST_METHOD'] !== 'GET'
    || $mode !== 'update'
    || !fn_get_runtime_company_id()
) {
    return;
}

$view = Tygh::$app['view'];
$current_plan = $view->getTemplateVars('current_plan');

if (!$current_plan) {
    return;
}

// The company settings page in the partner cabinet is a read-only plan page.
$_REQUEST['selected_section'] = 'plan';
$view->assign('selected_section', 'plan');
$view->assign('vendor_plans', [$current_plan]);
$view->assign('talario_vendor_plan_readonly', true);

Registry::set('navigation.tabs', [
    'plan' => [
        'title' => __('vendor_plans.plan'),
        'js'    => true,
    ],
]);
