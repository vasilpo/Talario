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

use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode === 'get_custom_file') {
    if (
        !isset($_REQUEST['hash'])
        || !isset($_REQUEST['object_type'])
        || !isset($_REQUEST['object_id'])
        || !isset($_REQUEST['field_id'])
    ) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $object_type = $_REQUEST['object_type'];
    $object_id = $_REQUEST['object_id'];
    $field_id = $_REQUEST['field_id'];
    $hash = $_REQUEST['hash'];

    $field_data = fn_get_profile_field_data($object_type, $object_id, $field_id);

    if (empty($field_data['file_path']) || $hash !== $field_data['hash']) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    if (!Storage::instance('custom_files')->isExist($field_data['file_path'])) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    Storage::instance('custom_files')->get($field_data['file_path']);
}
