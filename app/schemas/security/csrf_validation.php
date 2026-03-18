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

/**
 * Describes anti-CSRF validation requirements (see ::fn_csrf_validate_request()).
 *
 * Syntax:
 * 'area' => [
 *     'validate' => true/false,            // General area validation rule
 *     'controllers' => [
 *         'validate' => true/false,        // General controller validation rule
 *         'modes' => [
 *             'mode' => [
 *                 'validate' => true/false // Specific mode validation rule
 *             ]
 *         ]
 *     ]
 * ]
 *
 * When validating a request, the rules are applied in the following order:
 * 1. Specific mode validation rule
 * 2. General controller validation rule (if the previous one is not found)
 * 3. General area validation rule (if the previous ones are not found)
 */
$schema = [
    'A' => [
        'validate'    => true,
        'controllers' => [
            'payment_notification' => [
                'validate' => false,
            ],
        ],
    ],
    'C' => [
        'validate'    => false,
        'controllers' => [
            'payment_notification' => [
                'validate' => false,
            ],
            'auth'                 => [
                'validate' => true,
            ],
            'profiles'             => [
                'validate' => true,
            ],
            'checkout'             => [
                'validate' => true,
            ],
            'orders'               => [
                'validate' => true,
            ],
        ],
    ],
];

return $schema;
