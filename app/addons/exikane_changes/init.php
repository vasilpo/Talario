<?php

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'get_product_data_post',
    'update_profile',
    'update_product_post',
    'delete_product_post'
);
