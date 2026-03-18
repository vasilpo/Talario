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


/**
 * Gets statuses list for statuses_paid setting.
 *
 * @return array Statuses
 */
function fn_settings_variants_addons_rus_online_cash_register_statuses_paid()
{
    return fn_get_simple_statuses(STATUSES_ORDER);
}

/**
 * Gets statuses list for statuses_refund setting.
 *
 * @return array Statuses
 */
function fn_settings_variants_addons_rus_online_cash_register_statuses_refund()
{
    return fn_get_simple_statuses(STATUSES_ORDER);
}

/**
 * Gets statuses list for statuses_prepaid setting.
 *
 * @return array Statuses
 */
function fn_settings_variants_addons_rus_online_cash_register_statuses_prepaid()
{
    return fn_get_simple_statuses(STATUSES_ORDER);
}

/**
 * Gets currencies list for currency setting.
 */
function fn_settings_variants_addons_rus_online_cash_register_currency()
{
    $result = array();
    $currencies = fn_get_currencies_list();

    foreach ($currencies as $code => $item) {
        $result[$code] = $item['description'];
    }

    return $result;
}

/**
 * Gets taxation systems list for sno setting.
 */
function fn_settings_variants_addons_rus_online_cash_register_sno()
{
    $result = array();
    $schema = fn_get_schema('rus_online_cash_register', 'sno');

    foreach ($schema as $key => $item) {
        $result[$key] = $item['name'];
    }

    return $result;
}