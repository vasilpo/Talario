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


/**
 * Prepares array of available vendor administrator user groups
 *
 * @return array
 */
function fn_settings_variants_addons_vendor_privileges_default_vendor_usesrgroup()
{
    $variants = [
        0 => __('none'),
    ];

    if (!defined('USERGROUP_TYPE_VENDOR')) {
        return $variants;
    }

    $params = [
        'type'   => USERGROUP_TYPE_VENDOR,
        'status' => 'A',
    ];

    $usergroups = fn_get_usergroups($params);

    foreach ($usergroups as $usergroup) {
        $variants[$usergroup['usergroup_id']] = $usergroup['usergroup'];
    }

    return $variants;
}