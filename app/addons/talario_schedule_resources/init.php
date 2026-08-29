<?php

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks('delete_product_post', 'pre_add_to_cart', 'pre_place_order', 'order_placement_routines', 'change_order_status');
