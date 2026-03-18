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

use Tygh\Enum\ReceiverSearchMethods;
use Tygh\Enum\UserTypes;

defined('BOOTSTRAP') or die('Access denied');

/**
 * This schema describes the availability and appearance of the notification receivers editor on the Administration > Notifications page.
 * Event groups that are not present in this schema will use the default specification (`__default`).
 *
 * The syntax of the schema is the following:
 * [
 *    (stirng) {GroupId} => [
 *        (string) {ReceiverType} => [
 *            'is_configurable' => bool {IsReceiverConfigurable},
 *            'methods' => [
 *                (string) {ReceiverSearchMethod} => bool {IsReceiverSeachMethodAvailable},
 *            ]
 *        ]
 *    ]
 * ]
 *
 * - {GroupId} - event group identifier
 * - {ReceiverType} — receiver type identifier (@see \Tygh\Enum\UserTypes)
 * - {IsReceiverConfigurable} — whether notification receivers editor is available at all for the specified event group and receiver type
 * - {ReceiverSearchMethod} — receiver search method (@see \Tygh\Enum\ReceiverSearchMethods)
 * - {IsReceiverSeachMethodAvailable} - whether specified receiver search method is available in the receivers editor
 */
$schema = [
    '__default' => [
        UserTypes::ADMIN => [
            'is_configurable' => true,
            'methods'         => [
                ReceiverSearchMethods::USERGROUP_ID => true,
                ReceiverSearchMethods::USER_ID      => true,
                ReceiverSearchMethods::EMAIL        => true,
            ],
        ],
    ],

    'profile' => [],
];

return $schema;
