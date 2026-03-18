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

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['controllers']['order_management']['permissions'] = true;
$schema['controllers']['order_management']['condition'] = [
    'operator' => 'and',
    'function' => ['fn_vendor_privileges_check_permission_order_management'],
];

if (!is_array($schema['controllers']['exim']['modes']['import']['param_permissions']['section']['orders'])) {
    $schema['controllers']['exim']['modes']['import']['param_permissions']['section']['orders'] = [];
}
$schema['controllers']['exim']['modes']['import']['param_permissions']['section']['orders']['permissions'] = true;
$schema['controllers']['exim']['modes']['import']['section']['orders']['condition'] = [
    'operator' => 'and',
    'function' => ['fn_vendor_privileges_check_permission_order_management'],
];
$schema['import']['sections']['orders']['permission'] = true;
$schema['controllers']['profiles']['modes']['act_as_user']['permissions'] = 'manage_users';
unset($schema['controllers']['discussion']['modes']['products_and_pages']);
return $schema;
