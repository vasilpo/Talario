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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataProviders\OrderDataProvider;
use Tygh\Notifications\DataProviders\ProfileDataProvider;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Internal\InternalMessageSchema;
use Tygh\Notifications\Transports\Internal\InternalTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\NotificationsCenter\NotificationsCenter;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

require_once __DIR__ . '/events.functions.php';

/**
 * This schema contains all notification events in the product.
 *
 * The schema has the following structure:
 * (string) EventId => [                                                    //Eventid is an event identifier. It is used as the first argument in @see \Tygh\Notifications\EventDispatcher::dispatch.
 *     'group' => (string) GroupId,                                         //GroupId is an event group identifier. It is using for navigation at notification settings page
 *     'name' => [
 *         'template' => (string) TemplateLanguageVariable,                 //TemplateLanguageVariable is a name of language variable that contains name of this event.
 *         'params' => [
 *             (string) SubstitutionName => (string) Substitution           //SubstitutionName is a name of parameter that can be sent to language variable. It is using for detail customization
 *             ...                                                          //Substitution is a value of SubstitutionName parameter.
 *         ],
 *     ],
 *     'data_provider' => (callable) DataProvider,                          //DataProvider implements interface \Tygh\Notification\DataProviders\DataProvider.
 *                                                                          //It is used for getting specific parameters for event based on data from the notification
 *     'receivers' => [
 *         (string) ReceiverId => [                                         //ReceiverId is a notification receiver identifier. It can be obtained from @see \fn_get_notification_rules.
 *              (string) TransportId => BaseMessageSchema::create([         //TransportId is a notification transport identifier. It can be obtained from @see \Tygh\Notifications\Transports\ITransport::getId.
 *                 'area'            => (string) area,                      //BaseMessageSchema instance of message schema with prepared for sending notification data.
 *                 'from'            => (string) from,                      //Input parameters contains the prepared data like text(area, from, template_code)
 *                 'to'              => (callable) DataValue::create(key),  //or instance of DataValue. It allows to get element of `data` array by divided key.
 *                 'template_code'   => (string) template_code,
 *                 ...
 *                 'language_code'   => (callable) DataValue::create(parent_key.key, default_value), //DataValue can have default_value.
 *                                                                                                   //This construction return element $data['parent_key']['key'] or default_value if it doesn't exist
 *                 'data_modifier'   => (callable) function (array $data) {                          //Callable function that allows to get specific parameters
 *                                                                                                   //for this type of transport based on data from the notification
 *                     return array_merge($data, $added_data_value);
 *                 }
 *             ]),
 *             ...
 *         ],
 *         ...
 *     ],
 *     'preview_data' => [...],                                             //Data for preview and test email
 * ],
 * ...
 */
$schema = [
    'order.shipment_updated' => [
        'group'     => 'orders',
        'name'      => [
            'template' => 'event.order.shipment_updated.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_orders_department',
                    'to'              => DataValue::create('order_info.email'),
                    'template_code'   => 'shipment_products',
                    'legacy_template' => 'shipments/shipment_products.tpl',
                    'company_id'      => DataValue::create('order_info.company_id'),
                    'storefront_id'   => DataValue::create('order_info.storefront_id'),
                    'language_code'   => DataValue::create('order_info.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'order_info' => [
                'order_id'  => 1,
                'firstname' => 'First name',
                'products'  => [
                    [
                        'product'         => 'Product name',
                        'product_options' => [
                            [
                                'option_name'  => 'Option name',
                                'variant_name' => 'Variant name',
                            ],
                        ],
                    ],
                ],
            ],
            'shipment' => [
                'shipping'        => 'Shipping method',
                'timestamp'       => TIME,
                'carrier_info'    => [
                    'name'         => 'Carrier name',
                    'tracking_url' => 'https://example.com',
                    'info'         => 'Carrier info',
                ],
                'tracking_number' => 'ABC1234567890',
                'products'        => [1],
                'comments'        => 'Shipment comments',
            ],
        ],
    ],
    'order.edp' => [
        'group'     => 'orders',
        'name'      => [
            'template' => 'event.order.edp.name',
            'params'   => [],
        ],
        'data_provider' => static function (array $data) {
            return array_merge($data, [
                'order_files_list_url' => fn_url(
                    "orders.order_downloads&order_id={$data['order_info']['order_id']}&storefront_id={$data['order_info']['storefront_id']}",
                    SiteArea::STOREFRONT
                ),
            ]);
        },
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_orders_department',
                    'to'              => DataValue::create('order_info.email'),
                    'template_code'   => 'edp_access',
                    'legacy_template' => 'orders/edp_access.tpl',
                    'company_id'      => DataValue::create('order_info.company_id'),
                    'storefront_id'   => DataValue::create('order_info.storefront_id'),
                    'language_code'   => DataValue::create('order_info.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'order_info'           => [
                'order_id'  => 1,
                'firstname' => 'First name',
            ],
            'order_files_list_url' => 'https://example.com',
            'edp_data'             => [
                [
                    'files' => [
                        [
                            'file_name' => 'File name',
                            'url'       => 'https://example.com',
                            'file_size' => 1024,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'product.back_in_stock' => [
        'group'     => 'products',
        'name'      => [
            'template' => 'event.product.back_in_stock.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_orders_department',
                    'reply_to'        => 'company_orders_department',
                    'to'              => DataValue::create('subscribers'),
                    'template_code'   => 'back_in_stock_notification',
                    'legacy_template' => 'product/back_in_stock_notification.tpl',
                    'company_id'      => DataValue::create('company_id'),
                    'storefront_id'   => DataValue::create('storefront_id'),
                    'language_code'   => DataValue::create('lang_code', CART_LANGUAGE),
                ]),
            ],
        ],
        'preview_data' => [
            'product' => [
                'name' => 'Product name',
            ],
            'url'     => 'https://example.com',
        ],
    ],

    'profile.activated.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.activated.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'profile_activated',
                    'legacy_template' => 'profiles/profile_activated.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data' => [
                'firstname' => 'First name',
            ],
        ],
    ],
    'profile.activated.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.activated.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'profile_activated',
                    'legacy_template' => 'profiles/profile_activated.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'firstname' => 'First name',
        ],
    ],
    'profile.deactivated.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.deactivated.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'profile_deactivated',
                    'legacy_template' => 'profiles/profile_deactivated.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data' => [
                'firstname' => 'First name',
            ],
        ],
    ],
    'profile.deactivated.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.deactivated.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'profile_deactivated',
                    'legacy_template' => 'profiles/profile_deactivated.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'firstname' => 'First name',
        ],
    ],
    'profile.password_reminder' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.password_reminder.name',
            'params'   => [],
        ],
        'data_provider' => static function (array $data) {
            $user_data = $data['user_data'];

            $protocol = (Registry::get('settings.Security.secure_admin') === 'Y') ? 'https' : 'http';

            return array_merge($data, [
                'days'      => round((TIME - $user_data['password_change_timestamp']) / SECONDS_IN_DAY),
                'url'       => fn_url('auth.password_change', $user_data['user_type'], $protocol),
                'firstname' => !empty($user_data['firstname']) ? $user_data['firstname'] : fn_get_user_type_description($user_data['user_type']),
                'store_url' => Registry::get('config.' . $protocol . '_location'),
            ]);
        },
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'reminder',
                    'legacy_template' => 'profiles/reminder.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ])
            ],
        ],
        'preview_data' => [
            'firstname' => 'First name',
            'days'      => 7,
            'store_url' => 'https://example.com',
            'url'       => 'https://example.com',
        ],
    ],
    'profile.usergroup_request' => [
        'group'     => 'users',
        'name'      => [
            'template' => 'event.profile.usergroup_request.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'default_company_users_department',
                    'to'              => 'default_company_users_department',
                    'reply_to'        => DataValue::create('user_data.email'),
                    'template_code'   => 'usergroup_request',
                    'legacy_template' => 'profiles/usergroup_request.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => Registry::get('settings.Appearance.backend_default_language')
                ]),
            ],
        ],
        'preview_data' => [
            'user_data' => [
                'email'     => 'admin@example.com',
                'firstname' => 'First name',
                'lastname'  => 'Last name',
            ],
            'usergroup' => 'Admin user group',
        ],
    ],
    'profile.updated.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.updated.name',
            'params'   => [],
        ],
        'data_provider' => [ProfileDataProvider::class, 'factory'],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'update_profile',
                    'legacy_template' => 'profiles/update_profile.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'storefront_id'   => DataValue::create('storefront_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data'       => [
                'email'       => 'admin@example.com',
                'firstname'   => 'First name',
                'b_firstname' => 'Billing first name',
                'b_lastname'  => 'Billing last name',
                'b_address_2' => 'Billing address 2',
                'b_address'   => 'Billing address',
                'b_city'      => 'Billing city',
                'b_country'   => 'Billing country',
                'b_phone'     => 'Billing phone',
                's_firstname' => 'Shipping first name',
                's_lastname'  => 'Shipping last name',
                's_address_2' => 'Shipping address 2',
                's_address'   => 'Shipping address',
                's_city'      => 'Shipping city',
                's_country'   => 'Shipping country',
                's_phone'     => 'Shipping phone',
            ],
            'login_url'       => 'https://example.com',
            'forgot_pass_url' => 'https://example.com',
        ],
    ],
    'profile.updated.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.updated.name',
            'params'   => [],
        ],
        'data_provider' => [ProfileDataProvider::class, 'factory'],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'update_profile',
                    'legacy_template' => 'profiles/update_profile.tpl',
                    'company_id'      => 0,
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'storefront_id'   => DataValue::create('storefront_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data'         => [
                'firstname' => 'First name',
                'email'     => 'admin@example.com',
            ],
            'api_access_status' => true,
            'login_url'         => 'https://example.com',
            'forgot_pass_url'   => 'https://example.com',
        ],
    ],

    'profile.created.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.created.name',
            'params'   => [],
        ],
        'data_provider' => [ProfileDataProvider::class, 'factory'],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'create_profile',
                    'legacy_template' => 'profiles/create_profile.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data'       => [
                'firstname' => 'First name',
                'email'     => 'customer@example.com',
            ],
            'login_url'       => 'https://example.com',
            'forgot_pass_url' => 'https://example.com',
        ],
    ],
    'profile.created.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.created.name',
            'params'   => [],
        ],
        'data_provider' => [ProfileDataProvider::class, 'factory'],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'create_profile',
                    'legacy_template' => 'profiles/create_profile.tpl',
                    'company_id'      => 0,
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE)
                ]),
            ],
        ],
        'preview_data' => [
            'user_data'       => [
                'firstname' => 'First name',
                'email'     => 'admin@example.com',
            ],
            'login_url'       => 'https://example.com',
            'forgot_pass_url' => 'https://example.com',
            'password'        => 'password'
        ],
    ],

    'profile.added' => [
        'group'     => 'users',
        'name'      => [
            'template' => 'event.profile.added.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => 'company_users_department',
                    'reply_to'        => DataValue::create('user_data.email'),
                    'template_code'   => 'activate_profile',
                    'legacy_template' => 'profiles/activate_profile.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
                    'data_modifier'   => static function (array $data) {
                        return array_merge($data, [
                            'url' => fn_url('profiles.update?user_id=' . $data['user_data']['user_id'], 'A'),
                        ]);
                    }
                ]),
            ],
        ],
        'preview_data' => [
            'user_data' => [
                'email' => 'admin@example.com',
            ],
            'url'       => 'https://example.com',
        ],
    ],

    'profile.usergroup_activation.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.usergroup_activation.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'usergroup_activation',
                    'legacy_template' => 'profiles/usergroup_activation.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'data_modifier'   => 'fn_event_profile_usergroup_state_updated_data_modifer'
                ]),
            ],
        ],
        'preview_data' => [
            'usergroups' => 'Customer user group',
        ],
    ],
    'profile.usergroup_disactivation.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.usergroup_disactivation.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'usergroup_disactivation',
                    'legacy_template' => 'profiles/usergroup_disactivation.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'data_modifier'   => 'fn_event_profile_usergroup_state_updated_data_modifer'
                ]),
            ],
        ],
        'preview_data' => [
            'usergroups' => 'Customer user group',
        ],
    ],
    'profile.usergroup_activation.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.usergroup_activation.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'usergroup_activation',
                    'legacy_template' => 'profiles/usergroup_activation.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'data_modifier'   => 'fn_event_profile_usergroup_state_updated_data_modifer'
                ]),
            ],
        ],
        'preview_data' => [
            'usergroups' => 'Admin user group',
        ],
    ],
    'profile.usergroup_disactivation.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.usergroup_disactivation.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'usergroup_disactivation',
                    'legacy_template' => 'profiles/usergroup_disactivation.tpl',
                    'company_id'      => DataValue::create('user_data.company_id'),
                    'to_company_id'   => DataValue::create('user_data.company_id'),
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'data_modifier'   => 'fn_event_profile_usergroup_state_updated_data_modifer'
                ]),
            ],
        ],
        'preview_data' => [
            'usergroups' => 'Admin user group',
        ],
    ],

    'profile.password_recover.c' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.password_recovery.name',
            'params'   => [
            ],
        ],
        'receivers' => [
            UserTypes::CUSTOMER => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::STOREFRONT,
                    'from'            => 'default_company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'recover_password',
                    'legacy_template' => 'profiles/recover_password.tpl',
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'storefront_id'   => DataValue::create('storefront_id'),
                    'data_modifier'   => static function (array $data) {
                        return array_merge($data, [
                            'url' => fn_url('auth.recover_password?ekey=' . $data['ekey'], 'C'),
                        ]);
                    }
                ]),
            ],
        ],
        'preview_data' => [
            'url' => 'https://example.com',
        ],
    ],
    'profile.password_recover.a' => [
        'group'     => 'profile',
        'name'      => [
            'template' => 'event.profile.password_recovery.name',
            'params'   => [
            ],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'default_company_users_department',
                    'to'              => DataValue::create('user_data.email'),
                    'template_code'   => 'recover_password',
                    'legacy_template' => 'profiles/recover_password.tpl',
                    'language_code'   => DataValue::create('user_data.lang_code', CART_LANGUAGE),
                    'storefront_id'   => DataValue::create('storefront_id'),
                    'data_modifier'   => static function (array $data) {
                        return array_merge($data, [
                            'url' => fn_url('auth.recover_password?ekey=' . $data['ekey'], 'A'),
                        ]);
                    }
                ]),
            ],
        ],
        'preview_data' => [
            'url' => 'https://example.com',
        ],
    ],
    'system.realtime_shipping_error.a' => [
        'group'     => 'system',
        'name'      => [
            'template' => 'event.system.realtime_shipping_error.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                MailTransport::getId() => MailMessageSchema::create([
                    'area'            => SiteArea::ADMIN_PANEL,
                    'from'            => 'company_site_administrator',
                    'to'              => 'default_company_site_administrator',
                    'template_code'   => 'shipping_error',
                    'legacy_template' => 'shipping/shipping_error.tpl',
                    'language_code'   => Registry::get('settings.Appearance.backend_default_language')
                ]),
            ],
        ],
        'preview_data' => [
            'log_message' => 'Error message',
        ],
    ],
    'helpdesk_process_message' => [
        'group'     => 'system',
        'name'      => [
            'template' => 'event.system.helpdesk_process_message.name',
            'params'   => [],
        ],
        'receivers' => [
            UserTypes::ADMIN => [
                InternalTransport::getId() => InternalMessageSchema::create([
                    'tag'           => DataValue::create('tag', NotificationsCenter::TAG_OTHER),
                    'title'         => DataValue::create('title'),
                    'message'       => DataValue::create('text'),
                    'severity'      => DataValue::create('type', NotificationSeverity::NOTICE),
                    'section'       => DataValue::create('section', NotificationsCenter::SECTION_OTHER),
                    'area'          => SiteArea::ADMIN_PANEL,
                    'action_url'    => DataValue::create('action_url'),
                    'language_code' => Registry::get('settings.Appearance.backend_default_language'),
                    'pinned'        => DataValue::create('pinned', false),
                    'remind'        => DataValue::create('remind', false),
                ]),
            ],
        ],
    ],
];


$order_event = [
    'id'        => 'order.status_changed',
    'group'     => 'orders',
    'name'      => [
        'template' => 'event.order.status_changed.name',
        'params'   => [
            '[status]' => '',
        ],
    ],
    'data_provider' => [OrderDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::STOREFRONT,
                'from'            => 'company_orders_department',
                'to'              => DataValue::create('order_info.email'),
                'template_code'   => DataValue::create('template_code'),
                'legacy_template' => 'orders/order_notification.tpl',
                'company_id'      => DataValue::create('order_info.company_id'),
                'storefront_id'   => DataValue::create('order_info.storefront_id'),
                'language_code'   => DataValue::create('order_info.lang_code', CART_LANGUAGE)
            ]),
        ],
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'             => SiteArea::ADMIN_PANEL,
                'from'             => 'default_company_orders_department',
                'to'               => 'default_company_orders_department',
                'reply_to'         => DataValue::create('order_info.email'),
                'template_code'    => DataValue::create('template_code'),
                'legacy_template'  => 'orders/order_notification.tpl',
                'company_id'       => DataValue::create('order_info.company_id'),
                'to_company_id'    => DataValue::create('order_info.company_id'),
                'storefront_id'    => DataValue::create('order_info.storefront_id'),
                'to_storefront_id' => DataValue::create('order_info.storefront_id'),
                'language_code'    => DataValue::create('lang_code', CART_LANGUAGE)
            ])
        ],
    ],
    'preview_data' => [
        'order_info'   => [
            'order_id'  => 29,
            'firstname' => 'First name',
        ],
        'order_status' => [
            'description' => 'Status',
        ],
    ],
];

if (fn_allowed_for('MULTIVENDOR')) {
    $order_event['receivers'][UserTypes::VENDOR][MailTransport::getId()] = MailMessageSchema::create([
        'area'            => SiteArea::ADMIN_PANEL,
        'from'            => 'default_company_orders_department',
        'to'              => 'company_orders_department',
        'reply_to'        => DataValue::create('order_info.email'),
        'template_code'   => DataValue::create('template_code'),
        'legacy_template' => 'orders/order_notification.tpl',
        'company_id'      => 0,
        'to_company_id'   => DataValue::create('order_info.company_id'),
        'storefront_id'   => DataValue::create('order_info.storefront_id'),
        'language_code'   => DataValue::create('lang_code', CART_LANGUAGE)
    ]);
}

foreach (fn_get_simple_statuses() as $status_id => $status_description) {
    $status_id = strtolower($status_id);

    $order_change_status_event = $order_event;
    $order_change_status_event['id'] = "order.status_changed.{$status_id}";
    $order_change_status_event['name']['params']['[status]'] = $status_description;

    foreach ($order_event['receivers'] as $receiver => $transports) {
        $mail_message_schema = clone $transports[MailTransport::getId()];
        $mail_message_schema->template_code = "order_notification.{$status_id}";

        $order_change_status_event['receivers'][$receiver][MailTransport::getId()] = $mail_message_schema;
    }

    $schema[$order_change_status_event['id']] = $order_change_status_event;
    $schema[$order_change_status_event['id']]['preview_data']['order_status']['description'] = $status_description;
}

$order_updated_status_event = $order_event;
$order_updated_status_event['id'] = 'order.updated';
$order_updated_status_event['name']['template'] = 'event.order.updated.name';

$schema[$order_updated_status_event['id']] = $order_updated_status_event;

return $schema;
