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
    'variants' => [
        'via_payment_method' => [
            'description'               => __('rus_taxes.send_receipt_via_payment_method_description'),
            'all_payments'              => false,
            'allowed_processor_scripts' => []
        ],
        'dont_send' => [
            'description'               => __('rus_taxes.send_receipt_dont_send_description'),
            'all_payments'              => true,
            'allowed_processor_scripts' => []
        ]
    ],
    'types' => [
        'send_prepayment_receipt' => [],
        'send_full_payment_receipt' => [],
        'send_refund_receipt' => []
    ]
];
