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

use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

include_once __DIR__ . '/order_digital_marking_codes.functions.php';
include_once(Registry::get('config.dir.schemas') . 'exim/order_items.functions.php');

$schema = [
    'section'    => 'orders',
    'pattern_id' => 'order_digital_marking_codes',
    'name'       => __('rus_taxes.order_digital_marking_codes'),
    'key'        => ['code_id'],
    'order'      => 1,
    'table'      => 'order_digital_marking_codes',
    'permissions' => [
        'import' => 'edit_order',
        'export' => 'view_orders',
    ],
    'references' => [
        'orders' => [
            'reference_fields'          => ['order_id' => '&order_id'],
            'join_type'                 => 'LEFT',
            'alt_key'                   => ['order_id'],
            'import_skip_db_processing' => true
        ],
        'order_details' => [
            'reference_fields'          => [
                'item_id'  => '&order_item_id',
                'order_id' => '&order_id',
            ],
            'join_type'                 => 'LEFT',
            'alt_key'                   => ['item_id'],
            'import_skip_db_processing' => true
        ],
    ],
    'condition' => [
        'conditions' => [
            '&orders.is_parent_order' => YesNo::NO,
        ],
    ],
    'range_options' => [
        'selector_url' => 'orders.manage',
        'object_name'  => __('orders'),
    ],
    'import_process_data' => [
        'clear_previous_marking_codes' => [
            'function'    => 'fn_rus_taxes_exim_clear_previous_marking_codes',
            'args'        => ['$object', '$primary_object_id'],
            'import_only' => true,
        ],
        'check_order_existence' => [
            'function'    => 'fn_check_order_existence',
            'args'        => ['$primary_object_id', '$object', '$pattern', '$options', '$processed_data', '$processing_groups', '$skip_record'],
            'import_only' => true,
        ],
        'check_order_item_existence' => [
            'function'    => 'fn_rus_taxes_exim_check_order_item_existence',
            'args'        => ['$object', '$processed_data', '$skip_record'],
            'import_only' => true,
        ],
        'check_marking_code_format' => [
            'function'    => 'fn_rus_taxes_exim_check_marking_code_format',
            'args'        => ['$object', '$processed_data', '$skip_record'],
            'import_only' => true,
        ],
        'check_marking_code' => [
            'function'    => 'fn_rus_taxes_exim_check_marking_code',
            'args'        => ['$object', '$processed_data', '$skip_record'],
            'import_only' => true,
        ],
    ],
    'export_fields' => [
        'Code ID' => [
            'db_field' => 'code_id',
            'alt_key'  => true,
            'required' => true,
        ],
        'Order ID' => [
            'db_field' => 'order_id',
            'required' => true,
        ],
        'Order item ID' => [
            'db_field' => 'order_item_id',
            'required' => true,
        ],
        'Marking code format' => [
            'db_field' => 'marking_code_format',
            'required' => true,
        ],
        'Marking code' => [
            'db_field' => 'marking_code',
            'required' => true,
        ],
    ],
];

$company_id = fn_get_runtime_company_id();
if ($company_id > 0) {
    $schema['condition']['conditions']['&orders.company_id'] = $company_id;
}

return $schema;
