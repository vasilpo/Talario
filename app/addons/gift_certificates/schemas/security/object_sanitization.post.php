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

use Tygh\Tools\SecurityHelper;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['gift_certificates'] = [
    SecurityHelper::SCHEMA_SECTION_FIELD_RULES => [
        'recipient' => SecurityHelper::ACTION_SANITIZE_HTML,
        'sender' => SecurityHelper::ACTION_SANITIZE_HTML,
        'message' => SecurityHelper::ACTION_SANITIZE_HTML,
        'address' => SecurityHelper::ACTION_SANITIZE_HTML,
        'address_2' => SecurityHelper::ACTION_SANITIZE_HTML,
        'city' => SecurityHelper::ACTION_SANITIZE_HTML,
    ]
];

return $schema;
