<?php

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $mode === 'index' && fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_REDIRECT, 'talario_dashboard.manage'];
}
