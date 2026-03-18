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

$schema = array(
    'yandex_money_postponed_order_status' => array(
        'type' => 'O', // order
        'description' => __('addons.rus_payments.on_hold_status'),
        'email_subj' => __('addons.rus_payments.on_hold_status_email_subject'),
        'email_header' => __('addons.rus_payments.on_hold_status_email_header'),
        'params' => array(
            'color' => '#49afcd',
            'inventory' => 'D',
            'remove_cc_info' => 'N',
            'repay' => 'N',
            'appearance_type' => 'D',
        )
    ),
    'yandex_money_refunded_order_status' => array(
        'type' => 'O', // order
        'description' => __('addons.rus_payments.refunded_status'),
        'email_subj' => __('addons.rus_payments.refunded_status_email_subject'),
        'email_header' => __('addons.rus_payments.refunded_status_email_header'),
        'params' => array(
            'color' => '#ea9999',
            'inventory' => 'I',
            'remove_cc_info' => 'N',
            'repay' => 'N',
            'appearance_type' => 'D',
        )
    )
);

return $schema;
