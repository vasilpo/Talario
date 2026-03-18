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

include_once(Registry::get('config.dir.addons') . 'seo/schemas/exim/seo.functions.php');

$schema['references']['seo_names'] = array (
    'reference_fields' => array ('object_id' => '#key', 'type' => 'p', 'dispatch' => '', 'lang_code' => '#product_descriptions.lang_code'),
    'join_type' => 'LEFT',
    'import_skip_db_processing' => true
);

if (fn_allowed_for('ULTIMATE')) {
    $schema['references']['seo_names']['reference_fields']['company_id'] = '&company_id';
}

$schema['export_fields']['SEO name'] = array (
    'table' => 'seo_names',
    'db_field' => 'name',
    'process_put' => array ('fn_create_import_seo_name', '#key', 'p', '#this', '%Product name%', 0, '', '', '#lang_code', '%Store%'),
);

if (Registry::get('addons.seo.single_url') == 'N') {
    $schema['export_fields']['SEO name']['multilang'] = true;
}

return $schema;
