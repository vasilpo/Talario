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

function fn_exim_set_em_subscriber_company($subscriber_id, $company_name)
{
    $company_id = fn_exim_set_company('em_subscribers', 'subscriber_id', $subscriber_id, $company_name);
}

function fn_import_check_em_subscriber_company_id(&$primary_object_id, &$object, &$pattern, &$options, &$processed_data, &$processing_groups, &$skip_record)
{
    if (!empty($primary_object_id) && Registry::get('runtime.company_id')) {
        $company_id = db_get_field('SELECT company_id FROM ?:em_subscribers WHERE company_id = ?s', $primary_object_id);

        if ($company_id != Registry::get('runtime.company_id')) {
            $processed_data['S']++;
            $skip_record = true;
        }
    }
}
