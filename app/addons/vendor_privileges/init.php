<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Addons\VendorPrivileges\ServiceProvider;

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    'usergroup_types_get_list',
    'usergroup_types_get_map_user_type',
    'get_privileges_post',
    'check_editable_permissions_post',
    'check_can_usergroup_have_privileges_post',
    'change_company_status_before_mail',
    'update_profile',
    'get_payment_usergroups',
    'define_usergroups',
    'mve_check_permission_order_management',
    'update_company',
    'vendor_plan_update',
    'api_check_access',
    'delete_usergroups_pre',
    'update_usergroup_pre'
);
