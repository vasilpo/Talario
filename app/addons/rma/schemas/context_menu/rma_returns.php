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

use Tygh\ContextMenu\Items\GroupItem;

defined('BOOTSTRAP') or die('Access denied!');

return [
    'selectable_statuses' => [],
    'items'               => [
        'actions' => [
            'name'     => ['template' => 'actions'],
            'type'     => GroupItem::class,
            'items'    => [
                'bulk_print_packing_slip' => [
                    'name'                => ['template' => 'bulk_print_packing_slip'],
                    'dispatch'            => 'rma.bulk_slip_print',
                    'data'                => [
                        'action_class'      => 'cm-new-window',
                        'action_attributes' => [
                            'data-ca-pass-selected-object-ids-as' => 'return_ids',
                        ],
                    ],
                    'position'            => 10,
                ],
                'delete_selected'   => [
                    'name'                => ['template' => 'delete_selected'],
                    'dispatch'            => 'rma.m_delete_returns',
                    'data'                => [
                        'action_class'    => 'cm-confirm',
                        'menu_item_class' => 'mobile-hide',
                    ],
                    'position'            => 20,
                ],
            ],
            'position' => 20,
        ],
    ],
];
