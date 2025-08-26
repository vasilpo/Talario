<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

return [
    'shipments' => [
        'company_join_table' => 'orders',
        'join_by_from' => 'order_id',
        'join_by_to' => 'order_id',
        'join_via_stack' => [
            [
                'table' => 'shipment_items',
                'join_by_from' => 'shipment_id',
                'join_by_to' => 'shipment_id'
            ]
        ]
    ]
];
