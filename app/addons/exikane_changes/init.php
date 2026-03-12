<?php

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'get_product_data_post',
    'update_profile',
    'update_product_post',
    'delete_product_post',
    'set_point_payment',
    'get_order_info',
    'pre_get_orders',
    'get_orders',
    'get_orders_post'
);
