<?php

defined('BOOTSTRAP') or die('Access denied');

$runtime_company_id = fn_get_runtime_company_id();

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $mode === 'update'
    && $runtime_company_id
) {
    // A partner can view their plan, but cannot change it from the cabinet.
    unset($_REQUEST['company_data']['plan_id']);
}

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && $mode === 'balance'
    && $runtime_company_id
) {
    return [CONTROLLER_STATUS_REDIRECT, 'talario_finance.manage'];
}
