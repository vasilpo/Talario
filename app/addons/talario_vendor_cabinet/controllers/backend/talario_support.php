<?php

defined('BOOTSTRAP') or die('Access denied');

if (!fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_DENIED];
}
