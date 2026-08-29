<?php

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'vendor_data_premoderation_request_approval_for_products_pre',
    'vendor_data_premoderation_approve_products_pre'
);
