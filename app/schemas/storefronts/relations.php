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

return [
    'country_codes' => [
        'table'               => '?:storefronts_countries',
        'table_alias'         => 'countries',
        'type'                => 'value',
        'id_field'            => 'country_code',
        'storefront_id_field' => 'storefront_id',
    ],
    'company_ids'   => [
        'table'               => '?:storefronts_companies',
        'table_alias'         => 'companies',
        'type'                => 'value',
        'id_field'            => 'company_id',
        'storefront_id_field' => 'storefront_id',
    ],
    'currency_ids'  => [
        'table'               => '?:storefronts_currencies',
        'table_alias'         => 'currencies',
        'type'                => 'value',
        'id_field'            => 'currency_id',
        'storefront_id_field' => 'storefront_id',
        'related_table'       => '?:currencies',
        'related_id_field'    => 'currency_id',
    ],
    'language_ids'  => [
        'table'               => '?:storefronts_languages',
        'table_alias'         => 'languages',
        'type'                => 'value',
        'id_field'            => 'language_id',
        'storefront_id_field' => 'storefront_id',
        'related_table'       => '?:languages',
        'related_id_field'    => 'lang_id',
    ],
    'payment_ids'   => [
        'table'               => '?:storefronts_payments',
        'table_alias'         => 'payments',
        'type'                => 'value',
        'id_field'            => 'payment_id',
        'storefront_id_field' => 'storefront_id',
    ],
    'shipping_ids'   => [
        'table'               => '?:storefronts_shippings',
        'table_alias'         => 'shippings',
        'type'                => 'value',
        'id_field'            => 'shipping_id',
        'storefront_id_field' => 'storefront_id',
    ],
    'promotion_ids'   => [
        'table'               => '?:storefronts_promotions',
        'table_alias'         => 'promotions',
        'type'                => 'value',
        'id_field'            => 'promotion_id',
        'storefront_id_field' => 'storefront_id',
    ],
];
