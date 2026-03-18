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

use Tygh\Registry;

require_once Registry::get('config.dir.addons') . 'call_requests/schemas/block_manager/blocks.functions.php';

/**
 * @var array<string, array> $schema
 */

$schema['call_request'] = [
    'content' => [
        'phone_number' => [
            'type' => 'function',
            'function' => ['fn_call_requests_blocks_get_split_phone'],
        ]
    ],
    'templates' => 'addons/call_requests/blocks/call_request.tpl',
    'wrappers' => 'blocks/wrappers',
    'cache' => [
        'request_handlers' => ['%COMPANY_ID%']
    ],
];

return $schema;
