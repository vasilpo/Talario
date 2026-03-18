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

if (fn_allowed_for('MULTIVENDOR:ULTIMATE') && !empty(Tygh::$app['session']['auth']['storefront_id'])) {
    /** @var array $schema */
    $schema['category'][SecurityHelper::SCHEMA_SECTION_FIELD_RULES]['storefront_id'] = SecurityHelper::ACTION_SET_STOREFRONT_ID;
    $schema['shipping'][SecurityHelper::SCHEMA_SECTION_FIELD_RULES]['storefront_ids'] = SecurityHelper::ACTION_SET_STOREFRONT_ID;
    $schema['payment'][SecurityHelper::SCHEMA_SECTION_FIELD_RULES]['storefront_ids'] = SecurityHelper::ACTION_SET_STOREFRONT_ID;
    $schema['user'][SecurityHelper::SCHEMA_SECTION_FIELD_RULES]['storefront_id'] = SecurityHelper::ACTION_SET_STOREFRONT_ID;
}

return $schema;
