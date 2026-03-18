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

use Tygh\Addons\Rma\Notifications\DataProviders\ReturnRequestDataProvider;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Notifications\Transports\Mail\MailTransport;

defined('BOOTSTRAP') or die('Access denied');

$rma_event = [
    'id' => 'rma.status_changed',
    'group'     => 'rma',
    'name'      => [
        'template' => 'event.rma.status_changed.name',
        'params'   => [
            '[status]' => '',
        ],
    ],
    'data_provider' => [ReturnRequestDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::STOREFRONT,
                'from'            => 'company_orders_department',
                'to'              => DataValue::create('order_info.email'),
                'template_code'   => 'rma_slip_notification',
                'legacy_template' => 'addons/rma/slip_notification.tpl',
                'company_id'      => DataValue::create('order_info.company_id'),
                'to_company_id'   => DataValue::create('order_info.company_id'),
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE)
            ]),
        ],
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::ADMIN_PANEL,
                'from'            => 'default_company_orders_department',
                'to'              => 'default_company_orders_department',
                'reply_to'        => DataValue::create('order_info.email'),
                'template_code'   => 'rma_slip_notification',
                'legacy_template' => 'addons/rma/slip_notification.tpl',
                'company_id'      => DataValue::create('order_info.company_id'),
                'to_company_id'   => DataValue::create('order_info.company_id'),
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE)
            ])
        ],
    ],
    'preview_data' => [
        'return_info'   => [
            'return_id' => 1,
        ],
        'return_status' => [
            'email_subj'   => 'Subject',
            'email_header' => 'Header',
        ],
        'order_info'    => [
            'firstname' => 'First name',
        ],
    ],
];


if (fn_allowed_for('MULTIVENDOR')) {
    $rma_event['receivers'][UserTypes::VENDOR] = [
        MailTransport::getId() => MailMessageSchema::create(
            [
                'area'            => SiteArea::ADMIN_PANEL,
                'from'            => 'default_company_orders_department',
                'to'              => 'company_orders_department',
                'reply_to'        => DataValue::create('order_info.email'),
                'template_code'   => 'rma_slip_notification',
                'legacy_template' => 'addons/rma/slip_notification.tpl',
                'company_id'      => 0,
                'to_company_id'   => DataValue::create('order_info.company_id'),
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE),
            ]
        ),
    ];
}

foreach (fn_get_simple_statuses(STATUSES_RETURN) as $status_id => $status_description) {
    $status_id = strtolower($status_id);

    $rma_change_status_event = $rma_event;
    $rma_change_status_event['id'] = "rma.status_changed.{$status_id}";
    $rma_change_status_event['name']['params']['[status]'] = $status_description;

    $schema[$rma_change_status_event['id']] = $rma_change_status_event;
}

return $schema;
