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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\VendorPayoutApprovalStatuses;
use Tygh\Enum\VendorPayoutTypes;
use Tygh\Enum\VendorStatuses;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Tools\Url;
use Tygh\UpgradeCenter\App;

defined('BOOTSTRAP') or die('Access denied');

return [
    'vendor_status_suspended' => [
        'type'             => NotificationSeverity::WARNING,
        'area'             => SiteArea::VENDOR_PANEL,
        'content_callback' => static function ($auth) {
            $data = [];
            $vendor_data = fn_get_company_data($auth['company_id']);

            if ($vendor_data['status'] === VendorStatuses::SUSPENDED) {
                $data['text'] = __('dashboard.todo.suspend_alert');
            }

            return $data;
        }
    ],
    'vendor_status_pending' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::VENDOR_PANEL,
        'content_callback' => static function ($auth) {
            $data = [];
            $vendor_data = fn_get_company_data($auth['company_id']);

            if ($vendor_data['status'] === VendorStatuses::PENDING) {
                $data['text'] = __('dashboard.todo.vendor_status_pending');
            }

            return $data;
        }
    ],
    'vendor_products_without_category' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::VENDOR_PANEL,
        'content_callback' => static function ($auth) {
            $default_category_id = fn_get_default_category_id(0);
            if ($default_category_id === false) {
                return false;
            }

            $amount = db_get_field(
                'SELECT product_count FROM ?:category_vendor_product_count'
                . ' WHERE company_id = ?s AND category_id = ?s',
                $auth['company_id'],
                $default_category_id
            );
            if (!$amount) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.has_products_without_category', [$amount]),
                'action_url' => Url::buildUrn(['products', 'manage'], ['cid' => $default_category_id])
            ];
        }
    ],
    'admin_products_without_category' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {
            $default_category_ids = fn_get_all_default_categories_ids();
            if (empty($default_category_ids)) {
                return false;
            }

            $amount = db_get_field(
                'SELECT product_count FROM ?:categories WHERE category_id IN (?n)',
                $default_category_ids
            );
            if (!$amount) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.has_products_without_category', [$amount]),
                'action_url' => Url::buildUrn(['products', 'manage'], ['cid' => implode(',', $default_category_ids)])
            ];
        }
    ],
    'new_vendors' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {
            if (!fn_allowed_for('MULTIVENDOR')) {
                return false;
            }

            $companies_count = db_get_field(
                'SELECT COUNT(*) FROM ?:companies WHERE status = ?s',
                VendorStatuses::NEW_ACCOUNT
            );

            if (empty($companies_count)) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.new_vendors', [$companies_count]),
                'action_url' => Url::buildUrn(['companies', 'manage'], ['status' => VendorStatuses::NEW_ACCOUNT])
            ];
        }
    ],
    'vendor_withdrawal_requests' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {
            if (!fn_allowed_for('MULTIVENDOR') || !fn_check_permissions('companies', 'payouts', 'admin')) {
                return false;
            }

            $requests_count = db_get_field(
                'SELECT COUNT(*) FROM ?:vendor_payouts WHERE payout_type = ?s AND approval_status = ?s',
                VendorPayoutTypes::WITHDRAWAL,
                VendorPayoutApprovalStatuses::PENDING
            );

            if (empty($requests_count)) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.vendor_withdrawal_requests', [$requests_count]),
                'action_url' => Url::buildUrn(
                    ['companies', 'balance'],
                    ['selected_section' => 'withdrawals', 'approval_status' => VendorPayoutApprovalStatuses::PENDING]
                )
            ];
        }
    ],
    'helpdesk_account' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {

            $is_need_login = !$auth['helpdesk_user_id']
                && fn_check_permissions('addons', 'update', 'admin');

            if (!$is_need_login) {
                return false;
            }

            return [
                'action_text' => __('helpdesk_account.sign_in'),
                'text'        => __('helpdesk_account.signed_out_message'),
                'action_url'  => Tygh::$app['helpdesk.connect_url']
            ];
        }
    ],
    'upgrade_center_core_upgrade_available' => [
        'type'             => NotificationSeverity::WARNING,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {
            if (!fn_check_user_access($auth['user_id'], 'upgrade_store')) {
                return false;
            }
            $app = App::instance();
            $upgrade_packages = $app->getUpgradePackagesWithCache();

            if (empty($upgrade_packages['core']['core'])) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.upgrade_center_core_upgrade_available'),
                'action_url' => Url::buildUrn(['upgrade_center', 'manage'])
            ];
        }
    ],
    'upgrade_center_addons_upgrade_available' => [
        'type'             => NotificationSeverity::NOTICE,
        'area'             => SiteArea::ADMIN_PANEL,
        'content_callback' => static function ($auth) {
            if (!fn_check_user_access($auth['user_id'], 'upgrade_store')) {
                return false;
            }
            $app = App::instance();
            $upgrade_packages = $app->getUpgradePackagesWithCache();

            if (empty($upgrade_packages['addon'])) {
                return false;
            }

            return [
                'text' => __('dashboard.todo.upgrade_center_addons_upgrade_available'),
                'action_url' => Url::buildUrn(['upgrade_center', 'manage'])
            ];
        }
    ],
];
