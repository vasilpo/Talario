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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_rus_sberbank_install()
{
    fn_rus_sberbank_uninstall();

    $_data = array(
        'processor' => 'Сбербанк Онлайн',
        'processor_script' => 'sberbank.php',
        'processor_template' => 'views/orders/components/payments/cc_outside.tpl',
        'admin_template' => 'sberbank.tpl',
        'callback' => 'Y',
        'type' => 'P',
        'addon' => 'rus_sberbank'
    );

    db_query("INSERT INTO ?:payment_processors ?e", $_data);
}

function fn_rus_sberbank_uninstall()
{
    db_query("DELETE FROM ?:payment_processors WHERE processor_script = ?s", "sberbank.php");
}

function fn_rus_sberbank_normalize_phone($phone)
{
    $phone_normalize = '';

    if (!empty($phone)) {
        if (strpos('+', $phone) === false && $phone[0] == '8') {
            $phone[0] = '7';
        }

        $phone_normalize = str_replace(array(' ', '(', ')', '-'), '', $phone);
    }

    return $phone_normalize;
}

/**
 * The "get_payment_processors_post" hook handler.
 *
 * Actions performed:
 *     - Adds specific 'russian' attribute to some payment processors for categorization.
 *
 * @see \fn_get_payment_processors()
 */
function fn_rus_sberbank_get_payment_processors_post($lang_code, &$processors)
{
    foreach ($processors as &$processor) {
        if ($processor['addon'] === 'rus_sberbank') {
            $processor['russian'] = true;
        }
    }
    unset($processor);
}
