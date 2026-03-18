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

use Tygh\Registry;

include_once(Registry::get('config.dir.schemas') . 'exim/language_variables.functions.php');

return array(
    'section' => 'translations',
    'pattern_id' => 'language_variables',
    'name' => __('language_variables'),
    'key' => array('name', 'lang_code'),
    'order' => 1,
    'table' => 'language_values',
    'permissions' => array(
        'import' => 'manage_languages',
        'export' => 'view_languages',
    ),
    'condition' => array(
        'conditions' => array('lang_code' => '@lang_code'),
    ),
    'options' => array(
        'lang_code' => array(
            'title' => 'language',
            'type' => 'languages',
            'default_value' => array(DEFAULT_LANGUAGE),
        ),
    ),
    'export_fields' => array(
        'Name' => array(
            'db_field' => 'name',
            'alt_key' => true,
            'required' => true,
            'multilang' => true
        ),
        'Value' => array(
            'db_field' => 'value',
            'required' => true,
            'multilang' => true
        ),
        'Language' => array(
            'db_field' => 'lang_code',
            'alt_key' => true,
            'required' => true,
            'multilang' => true
        ),
    ),
    'import_process_data' => array(
        'check_lang_code' => array(
            'function' => 'fn_import_check_translations_lang_code',
            'args' => array('$primary_object_id', '$object', '$processed_data', '$skip_record'),
            'import_only' => true,
        ),
    ),
    'order_by' => 'name'
);
