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

function fn_import_date_to_timestamp($date = '')
{
    $date = !empty($date) ? $date : date("H:i:s");

    return strtotime($date);
}

function fn_export_convert_mailing_list($list_id, $lang_code)
{
    $lang_code = !empty($lang_code) ? $lang_code : DEFAULT_LANGUAGE;
    if (!empty($list_id)) {
        return db_get_field("SELECT object FROM ?:common_descriptions WHERE object_holder = 'mailing_lists' AND object_id = ?i AND lang_code = ?s", $list_id, $lang_code);
    } else {
        return '';
    }
}

function fn_import_convert_mailing_list($list_name)
{
    return db_get_field("SELECT object_id FROM ?:common_descriptions WHERE object_holder = 'mailing_lists' AND object = ?s", $list_name);
}

function fn_newsletters_import_after_process_data(&$primary_object_id, &$object, &$pattern, &$options, &$processed_data, &$processing_groups, &$skip_db_processing_record)
{
    if (empty($object['list_id'])) {
        fn_set_notification('W', __('warning'), __('warning_subscribers_import'));
        $skip_db_processing_record = true;
    }
}

