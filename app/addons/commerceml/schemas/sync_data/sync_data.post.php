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

include_once Registry::get('config.dir.addons') . 'commerceml/schemas/sync_data/sync_data.functions.php';

/**
 * @var array<string, string|array> $schema
 */
$schema['commerceml'] = [
    'name'            => __('commerceml.sync_data_name'),
    'update_template' => 'addons/commerceml/views/sync_data/components/update.tpl',
    'last_sync_info'  => [
        'function' => 'fn_sync_data_commerceml_get_last_sync_info',
    ],
];

return $schema;
