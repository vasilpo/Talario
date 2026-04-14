<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

Tygh::$app['class_loader']->add('', __DIR__);

fn_register_hooks(
    'init_storage',
    'init_templater_post',
    'update_addon_status_post'
);
