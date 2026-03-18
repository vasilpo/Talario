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

use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Registry;
use Tygh\Addons\CallRequests\Notifications\DataProviders\RequestCreatedDataProvider;

defined('BOOTSTRAP') or die('Access denied');

$schema['call_requests.request_created'] = [
    'group'     => 'call_requests',
    'name'      => [
        'template' => 'call_requests.event.request_created.name',
        'params'   => [],
    ],
    'data_provider' => [RequestCreatedDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'             => SiteArea::ADMIN_PANEL,
                'from'             => 'default_company_orders_department',
                'to'               => 'company_orders_department',
                'template_code'    => 'call_requests_call_request',
                'legacy_template'  => 'addons/call_requests/call_request.tpl',
                'company_id'       => DataValue::create('call_request_data.company_id'),
                'to_company_id'    => DataValue::create('call_request_data.company_id'),
                'to_storefront_id' => DataValue::create('call_request_data.storefront_id'),
                'language_code'    => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
    'preview_data' => [
        'customer'     => 'First name',
        'url'          => 'https://example.com',
        'phone_number' => '1234567890',
        'time_from'    => '10:00',
        'time_to'      => '16:00',
    ],
];

return $schema;
