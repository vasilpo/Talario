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
use Tygh\RusCurrency;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_rus_ruble_twg_get_all_settings(&$settings)
{
    if ($settings['currency']['currency_code'] == CURRENCY_RUB && $settings['currency']['symbol'] == SYMBOL_RUBL) {
        $settings['currency']['symbol'] = SYMBOL_RUBL_TEXT;
    }

    if ($settings['primaryCurrency']['currency_code'] == CURRENCY_RUB && $settings['primaryCurrency']['symbol'] == SYMBOL_RUBL) {
        $settings['primaryCurrency']['symbol'] = SYMBOL_RUBL_TEXT;
    }
}

function fn_rus_ruble_install()
{
    $currencies = Registry::get('currencies');
    if (empty($currencies)) {
        $currencies = fn_get_currencies_list(array(), 'A', CART_LANGUAGE);
        Registry::set('currencies', $currencies);
    }

    $magic_key = fn_rus_ruble_gen_magic_key();
    Settings::instance()->updateValue('cron_key', $magic_key, 'rus_ruble');

    RusCurrency::process_sbrf_currencies(CURRENCY_RUB);
}

function fn_rus_ruble_create()
{
    RusCurrency::rub_create();
    RusCurrency::process_sbrf_currencies(CART_PRIMARY_CURRENCY);
}

function fn_rus_ruble_sync($magic_key)
{
    $sync_status = SYNC_OK;
    $currencies = Registry::get('currencies');

    if (empty($currencies[CURRENCY_RUB])) {
        $sync_status = SYNC_NOT_SET_RUB;

    } elseif (!empty($magic_key)) {

        if (urldecode($magic_key) == Registry::get('addons.rus_ruble.cron_key')) {
            $result = RusCurrency::process_sbrf_currencies(CART_PRIMARY_CURRENCY);

            if (!$result) {
                $sync_status = SYNC_ERROR;
            }

        } else {
            $sync_status = SYNC_MAGIC_KEY_INCORRECT;
        }

    } else {
        $sync_status = SYNC_MAGIC_KEY_EMPTY;
    }

    return $sync_status;
}

function fn_rus_ruble_gen_magic_key()
{
    $magic_key = RusCurrency::currency_sync_generate_key(CRON_IMPORT_KEY_LENGTH);
    Registry::set('addons.rus_ruble.cron_key', $magic_key);

    return $magic_key;
}
