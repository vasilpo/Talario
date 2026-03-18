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

$schema['Google size'] = array(
    'option_field' => 'Y',
    'process_get' => array('fn_google_get_product_options', '#key', '#field', '#lang_code', '$lang_code'),
    'multilang' => true,
    'linked' => false,
    'option_class' => 'cm-google-option'
);

$schema['Google color'] = array(
    'option_field' => 'Y',
    'process_get' => array('fn_google_get_product_options', '#key', '#field', '#lang_code', '$lang_code'),
    'multilang' => true,
    'linked' => false,
    'option_class' => 'cm-google-option'
);

$schema['Google pattern'] = array(
    'option_field' => 'Y',
    'process_get' => array('fn_google_get_product_options', '#key', '#field', '#lang_code', '$lang_code'),
    'multilang' => true,
    'linked' => false,
    'option_class' => 'cm-google-option'
);

$schema['Google material'] = array(
    'option_field' => 'Y',
    'process_get' => array('fn_google_get_product_options', '#key', '#field', '#lang_code', '$lang_code'),
    'multilang' => true,
    'linked' => false,
    'option_class' => 'cm-google-option'
);

return $schema;
