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

use Tygh\Enum\Addons\VendorCommunication\CommunicationTypes;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Tools\Url;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array> $schema
 */

$schema['vendor_communication_admin_to_vendor_messages'] = [
    'type'             => NotificationSeverity::NOTICE,
    'area'             => SiteArea::VENDOR_PANEL,
    'content_callback' => static function ($auth) {
        $thread_ids = db_get_fields(
            'SELECT thread_id FROM ?:vendor_communications'
            . ' WHERE company_id = ?s AND status = ?s AND communication_type = ?s AND last_message_user_type = ?s',
            $auth['company_id'],
            VC_THREAD_STATUS_HAS_NEW_MESSAGE,
            CommunicationTypes::VENDOR_TO_ADMIN,
            UserTypes::ADMIN
        );

        if (empty($thread_ids)) {
            return false;
        }

        $threads_count = count($thread_ids);

        $action_url = fn_vendor_communitcation_generate_dashboard_action_url(
            'vendor_communication',
            'threads',
            CommunicationTypes::VENDOR_TO_ADMIN,
            $auth
        );

        if (!$action_url) {
            return false;
        }

        return [
            'text' => __('vendor_communication.dashboard.todo.messages_from_administrator', [$threads_count]),
            'action_url' => $action_url,
        ];
    }
];

$schema['vendor_communication_customer_to_vendor_messages'] = [
    'type'             => NotificationSeverity::NOTICE,
    'area'             => SiteArea::VENDOR_PANEL,
    'content_callback' => static function ($auth) {
        $thread_ids = db_get_fields(
            'SELECT thread_id FROM ?:vendor_communications'
            . ' WHERE company_id = ?s AND status = ?s AND communication_type = ?s AND last_message_user_type IN (?a)',
            $auth['company_id'],
            VC_THREAD_STATUS_HAS_NEW_MESSAGE,
            CommunicationTypes::VENDOR_TO_CUSTOMER,
            [UserTypes::CUSTOMER, UserTypes::ADMIN]
        );

        if (empty($thread_ids)) {
            return false;
        }

        $threads_count = count($thread_ids);

        $action_url = fn_vendor_communitcation_generate_dashboard_action_url(
            'vendor_communication',
            'threads',
            CommunicationTypes::VENDOR_TO_CUSTOMER,
            $auth
        );

        if (!$action_url) {
            return false;
        }

        return [
            'text' => __('vendor_communication.dashboard.todo.messages_from_customers', [$threads_count]),
            'action_url' => $action_url,
        ];
    }
];

$schema['vendor_communication_vendor_to_admin_messages'] = [
    'type'             => NotificationSeverity::NOTICE,
    'area'             => SiteArea::ADMIN_PANEL,
    'content_callback' => static function ($auth) {
        $thread_ids = db_get_fields(
            'SELECT thread_id FROM ?:vendor_communications'
            . ' WHERE status = ?s AND communication_type = ?s AND last_message_user_type = ?s',
            VC_THREAD_STATUS_HAS_NEW_MESSAGE,
            CommunicationTypes::VENDOR_TO_ADMIN,
            UserTypes::VENDOR
        );

        if (empty($thread_ids)) {
            return false;
        }

        $threads_count = count($thread_ids);

        $action_url = fn_vendor_communitcation_generate_dashboard_action_url(
            'vendor_communication',
            'threads',
            CommunicationTypes::VENDOR_TO_ADMIN,
            $auth
        );

        if (!$action_url) {
            return false;
        }

        return [
            'text' => __('vendor_communication.dashboard.todo.messages_from_vendors', [$threads_count]),
            'action_url' => $action_url,
        ];
    }
];

$schema['vendor_communication_customer_to_admin_messages'] = [
    'type'             => NotificationSeverity::NOTICE,
    'area'             => SiteArea::ADMIN_PANEL,
    'content_callback' => static function ($auth) {
        if (!fn_allowed_for('ULTIMATE')) {
            return false;
        }

        $thread_ids = db_get_fields(
            'SELECT thread_id FROM ?:vendor_communications'
            . ' WHERE status = ?s AND communication_type = ?s AND last_message_user_type = ?s',
            VC_THREAD_STATUS_HAS_NEW_MESSAGE,
            CommunicationTypes::VENDOR_TO_CUSTOMER,
            UserTypes::CUSTOMER
        );

        if (empty($thread_ids)) {
            return false;
        }

        $threads_count = count($thread_ids);

        $action_url = fn_vendor_communitcation_generate_dashboard_action_url(
            'vendor_communication',
            'threads',
            CommunicationTypes::VENDOR_TO_CUSTOMER,
            $auth
        );

        if (!$action_url) {
            return false;
        }

        return [
            'text' => __('vendor_communication.dashboard.todo.messages_from_customers', [$threads_count]),
            'action_url' => $action_url,
        ];
    }
];

return $schema;

/**
 * Generate action url for dasboard.
 *
 * @param string                                           $controller Dispatch controller.
 * @param string                                           $mode       Dispatch mode.
 * @param string                                           $type       Query param.
 * @param array<string, string|int|array<string|int, int>> $auth       Authentication data
 *
 * @return string|bool
 *
 * @psalm-suppress PossiblyInvalidArgument
 */
function fn_vendor_communitcation_generate_dashboard_action_url($controller, $mode, $type, $auth)
{
    $is_available = fn_check_permissions(
        $controller,
        $mode,
        'admin',
        '',
        [
            'dispatch' => $controller . '.' . $mode,
            'communication_type' => $type
        ],
        $auth['area'],
        $auth['user_id']
    );

    if ($is_available) {
        return Url::buildUrn(
            [$controller, $mode],
            ['communication_type' => $type]
        );
    }

    return false;
}
