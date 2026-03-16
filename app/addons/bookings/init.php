<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'get_order_info',
    'pre_get_orders',
    'get_orders',
    'get_orders_post'
);
