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

$schema['controllers']['payments'] = array(
    'permissions' => array(
        'GET'  => 'view_payments',
        'POST' => 'manage_payments',
    ),
);

$schema['controllers']['promotions'] = array(
    'permissions' => 'manage_promotions',
);

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['payments'] =
$schema['controllers']['tools']['modes']['update_position']['param_permissions']['table']['payments'] =
    array('permissions' => 'manage_payments');

$schema['controllers']['tools']['modes']['update_status']['condition']['table']['payments'] =
    array(
        'operator' => 'and',
        'function' => array('fn_direct_payments_check_payment_owner', null, $id),
    );

$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['promotions'] =
    array(
        'operator' => 'and',
        'function' => array('fn_direct_payments_check_promotion_owner', null, $id),
    );

return $schema;