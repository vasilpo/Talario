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

use Tygh\Registry;
use Tygh\Languages\Languages;

function fn_rus_personal_data_processing_information()
{
    $url_service = 'http://pd.rsoc.ru/operators-registry/notification/form/';
    $url_language = fn_url('languages.translations?q=addons.rus_personal_data_processing');

    $registry_description = __('addons.rus_personal_data_processing.updated_personal_data_registry_description', array('[url_service]' => $url_service, '[link]' => $url_language));

    return $registry_description;
}