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

/**
 * @var array<string, array<string, array>> $schema
 */

$schema['controllers']['debt']['modes']['refill_balance']['permissions'] = true;
$schema['controllers']['debt']['modes']['drop_plans_lowers_balance']['permissions'] = false;

if (isset($schema['controllers']['auth'])) {
    $schema['controllers']['auth']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['index'])) {
    $schema['controllers']['index']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['notifications'])) {
    $schema['controllers']['notifications']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['notifications_center'])) {
    $schema['controllers']['notifications_center']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['profiles'])) {
    $schema['controllers']['profiles']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['profiles']['modes']['manage'])) {
    $schema['controllers']['profiles']['modes']['manage']['permissions_blocked'] = false;
}

if (isset($schema['controllers']['companies']['modes'])) {
    $schema['controllers']['companies']['modes']['get_companies_list']
        = isset($schema['controllers']['companies']['modes']['get_companies_list'])
        ? $schema['controllers']['companies']['modes']['get_companies_list']
        : ['permissions' => true];
    $schema['controllers']['companies']['modes']['get_companies_list']['permissions_blocked'] = true;
}

if (isset($schema['controllers']['debt']['modes']['refill_balance'])) {
    $schema['controllers']['debt']['modes']['refill_balance']['permissions_blocked'] = true;
}

return $schema;
