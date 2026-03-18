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
use Tygh\Enum\StorefrontStatuses;
use Tygh\Http;

defined('BOOTSTRAP') or die('Access denied!');

return [
    'selectable_statuses' => [
        StorefrontStatuses::OPEN   => __('open'),
        StorefrontStatuses::CLOSED => __('close'),
    ],
    'items'               => [
        'status'  => [
            'name'                => ['template' => 'status'],
            'type'                => GroupItem::class,
            'items'               => [
                'change_to_status_open'  => [
                    'name'     => [
                        'template' => 'change_to_status',
                        'params'   => [
                            '[status]' => __('open'),
                        ],
                    ],
                    'dispatch' => 'companies.m_switch_storefront_status',
                    'data'     => [
                        'action_attributes' => [
                            'class'               => 'cm-ajax cm-post cm-ajax-send-form cm-confirm',
                            'href'                => fn_url('companies.m_switch_storefront_status?status=' . StorefrontStatuses::OPEN),
                            'data-ca-target-id'   => 'pagination_contents',
                            'data-ca-target-form' => '#companies_form',
                        ],
                    ],
                    'position' => 10,
                ],
                'change_to_status_close' => [
                    'name'     => [
                        'template' => 'change_to_status',
                        'params'   => [
                            '[status]' => __('close'),
                        ],
                    ],
                    'dispatch' => 'companies.m_switch_storefront_status',
                    'data'     => [
                        'action_attributes' => [
                            'class'               => 'cm-ajax cm-post cm-ajax-send-form cm-confirm',
                            'href'                => fn_url('companies.m_switch_storefront_status?status=' . StorefrontStatuses::CLOSED),
                            'data-ca-target-id'   => 'pagination_contents',
                            'data-ca-target-form' => '#companies_form',
                        ],
                    ],
                    'position' => 20,
                ]
            ],
            'permission_callback' => static function ($request, $auth, $runtime) {
                return !$runtime['company_id'] && fn_check_view_permissions('companies.update', Http::POST);
            },
            'position'            => 20,
        ],
        'actions' => [
            'name'     => ['template' => 'actions'],
            'type'     => GroupItem::class,
            'items'    => [
                'delete_selected'      => [
                    'name'                => ['template' => 'delete_selected'],
                    'dispatch'            => 'companies.m_delete',
                    'data'                => [
                        'action_class' => 'cm-confirm',
                    ],
                    'permission_callback' => static function ($request, $auth, $runtime) {
                        return !$runtime['company_id'] && fn_check_view_permissions('companies.update', Http::POST);
                    },
                    'position'            => 10,
                ],
            ],
            'position' => 30,
        ],
    ],
];
