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

use Tygh\Addons\CallRequests\Notifications\DataProviders\RequestAboutProductCreatedDataProvider;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$schema['call_requests.request_about_product_created'] = [
    'group'     => 'call_requests',
    'name'      => [
        'template' => 'call_requests.event.request_about_product_created.name',
        'params'   => [],
    ],
    'data_provider' => [RequestAboutProductCreatedDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::ADMIN_PANEL,
                'from'            => 'default_company_orders_department',
                'to'              => 'company_orders_department',
                'template_code'   => 'call_requests_buy_with_one_click',
                'legacy_template' => 'addons/call_requests/buy_with_one_click.tpl',
                'company_id'      => DataValue::create('call_request_data.company_id'),
                'to_company_id'   => DataValue::create('call_request_data.company_id'),
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
    'preview_data' => [
        'customer'     => 'First name',
        'url'          => 'https://example.com',
        'phone_number' => '1234567890',
        'product_url'  => 'https://example.com',
        'product_name' => 'Product name',
    ],
];

return $schema;
