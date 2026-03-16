<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'get_product_data_post',
    'update_product_post',
    'delete_product_post'
);
