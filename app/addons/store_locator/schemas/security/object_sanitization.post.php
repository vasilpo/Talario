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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Tools\SecurityHelper;

/**
 * @var array<string, array<string, array<string, string>>> $schema
 */
$schema['store_location'] = [
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => [
        'name'        => SecurityHelper::ACTION_REMOVE_HTML,
        'description' => SecurityHelper::ACTION_SANITIZE_HTML,
        'city'        => SecurityHelper::ACTION_REMOVE_HTML,
    ]
];

return $schema;
