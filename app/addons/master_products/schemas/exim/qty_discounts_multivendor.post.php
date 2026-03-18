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

defined('BOOTSTRAP') or die('Access denied');

require_once Registry::get('config.dir.addons') . 'master_products/schemas/exim/products.functions.php';

$runtime_company_id = Registry::get('runtime.company_id');

/**
 * @var array<string, array> $schema
 */
$schema['import_get_primary_object_id']['master_products_exim_set_company_id'] = [
    'function'    => 'fn_master_products_exim_set_company_id',
    'args'        => ['$alt_keys', '$skip_get_primary_object_id', $runtime_company_id],
    'import_only' => true,
];

return $schema;
