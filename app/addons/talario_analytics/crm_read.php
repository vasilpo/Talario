<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

function fn_talario_analytics_crm_rate_limit(string $provided_hash): void
{
    $now = time();
    $bucket = (int) floor($now / 60);
    $scopes = [
        [hash('sha256', 'crm_customer_360:global'), 20],
        [hash('sha256', 'crm_customer_360:credential:' . $provided_hash), 5],
    ];

    foreach ($scopes as [$scope_hash, $limit]) {
        db_query(
            'INSERT INTO ?:talario_analytics_rate_limits'
            . ' (scope_hash, minute_bucket, request_count, updated_at)'
            . ' VALUES (?s, ?i, 1, ?i)'
            . ' ON DUPLICATE KEY UPDATE request_count = request_count + 1, updated_at = ?i',
            $scope_hash,
            $bucket,
            $now,
            $now
        );
        $count = (int) db_get_field(
            'SELECT request_count FROM ?:talario_analytics_rate_limits'
            . ' WHERE scope_hash = ?s AND minute_bucket = ?i',
            $scope_hash,
            $bucket
        );
        if ($count > $limit) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario CRM customer-360 rate limit exceeded',
            ]);
            fn_talario_analytics_json_response(429, ['error' => 'rate_limit_exceeded']);
        }
    }
}

function fn_talario_analytics_crm_response(): void
{
    $after_user_id = max(0, (int) ($_GET['after_user_id'] ?? 0));
    $limit = (int) ($_GET['limit'] ?? 100);
    if ($limit < 1 || $limit > 100) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_limit', 'max_limit' => 100]);
    }

    $registered_from_raw = trim((string) ($_GET['registered_from'] ?? ''));
    $registered_from = null;
    if ($registered_from_raw !== '') {
        $registered_from = fn_talario_analytics_parse_date($registered_from_raw);
        if (!$registered_from) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_registered_from']);
        }
    }

    $user_query = 'SELECT u.user_id, u.email, u.firstname, u.lastname, u.status, u.timestamp,'
        . ' COALESCE(ud.data, 0) AS reward_points'
        . ' FROM ?:users u'
        . " LEFT JOIN ?:user_data ud ON ud.user_id = u.user_id AND ud.type = 'W'"
        . ' WHERE u.user_type = ?s AND u.user_id > ?i';
    $user_args = ['C', $after_user_id];

    if ($registered_from) {
        $user_query .= ' AND u.timestamp >= ?i';
        $user_args[] = $registered_from->setTime(0, 0, 0)->getTimestamp();
    }

    $user_query .= ' ORDER BY u.user_id ASC LIMIT ?i';
    $user_args[] = $limit + 1;

    $rows = db_get_array($user_query, ...$user_args);
    $has_more = count($rows) > $limit;
    if ($has_more) {
        $rows = array_slice($rows, 0, $limit);
    }

    $user_ids = array_map(static function (array $row): int {
        return (int) $row['user_id'];
    }, $rows);

    $timezone = new DateTimeZone('Europe/Moscow');
    $customers = [];
    foreach ($rows as $row) {
        $user_id = (int) $row['user_id'];
        $registered_at = (int) $row['timestamp'];
        $customers[$user_id] = [
            'user_id' => $user_id,
            'email' => (string) $row['email'],
            'firstname' => (string) $row['firstname'],
            'lastname' => (string) $row['lastname'],
            'status' => (string) $row['status'],
            'registered_at' => $registered_at > 0
                ? (new DateTimeImmutable('@' . $registered_at))->setTimezone($timezone)->format(DateTimeInterface::ATOM)
                : null,
            'reward_points' => (int) $row['reward_points'],
            'orders_count' => 0,
            'last_order_at' => null,
            'recent_order_products' => [],
            'cart_products' => [],
            'wishlist_products' => [],
            'newsletter_subscriptions' => [],
        ];
    }

    if ($user_ids) {
        foreach (db_get_array(
            'SELECT user_id, COUNT(*) AS orders_count, MAX(timestamp) AS last_order_at'
            . ' FROM ?:orders WHERE user_id IN (?n) GROUP BY user_id',
            $user_ids
        ) as $order_summary) {
            $user_id = (int) $order_summary['user_id'];
            if (!isset($customers[$user_id])) {
                continue;
            }
            $last_order_at = (int) $order_summary['last_order_at'];
            $customers[$user_id]['orders_count'] = (int) $order_summary['orders_count'];
            $customers[$user_id]['last_order_at'] = $last_order_at > 0
                ? (new DateTimeImmutable('@' . $last_order_at))->setTimezone($timezone)->format(DateTimeInterface::ATOM)
                : null;
        }

        $lang_code = (string) Registry::get('settings.Appearance.default_language');
        if ($lang_code === '') {
            $lang_code = 'ru';
        }

        $per_user_order_products = [];
        foreach (db_get_array(
            'SELECT o.user_id, o.order_id, o.status, o.timestamp, od.product_id, pd.product'
            . ' FROM ?:orders o'
            . ' INNER JOIN ?:order_details od ON od.order_id = o.order_id'
            . ' LEFT JOIN ?:product_descriptions pd ON pd.product_id = od.product_id AND pd.lang_code = ?s'
            . " WHERE o.user_id IN (?n) AND o.is_parent_order != 'Y'"
            . ' ORDER BY o.timestamp DESC, o.order_id DESC, od.item_id DESC LIMIT 2000',
            $lang_code,
            $user_ids
        ) as $item) {
            $user_id = (int) $item['user_id'];
            if (!isset($customers[$user_id])) {
                continue;
            }
            $count = $per_user_order_products[$user_id] ?? 0;
            if ($count >= 20) {
                continue;
            }
            $customers[$user_id]['recent_order_products'][] = [
                'order_id' => (int) $item['order_id'],
                'status' => (string) $item['status'],
                'timestamp' => (int) $item['timestamp'],
                'product_id' => (int) $item['product_id'],
                'product' => (string) ($item['product'] ?? ''),
            ];
            $per_user_order_products[$user_id] = $count + 1;
        }

        $per_user_session_products = [];
        foreach (db_get_array(
            'SELECT usp.user_id, usp.type, usp.product_id, usp.timestamp, pd.product'
            . ' FROM ?:user_session_products usp'
            . ' LEFT JOIN ?:product_descriptions pd ON pd.product_id = usp.product_id AND pd.lang_code = ?s'
            . " WHERE usp.user_id IN (?n) AND usp.type IN ('C', 'W') AND usp.item_type = 'P'"
            . ' ORDER BY usp.timestamp DESC LIMIT 2000',
            $lang_code,
            $user_ids
        ) as $item) {
            $user_id = (int) $item['user_id'];
            if (!isset($customers[$user_id])) {
                continue;
            }
            $count = $per_user_session_products[$user_id] ?? 0;
            if ($count >= 20) {
                continue;
            }
            $product = [
                'product_id' => (int) $item['product_id'],
                'product' => (string) ($item['product'] ?? ''),
                'timestamp' => (int) $item['timestamp'],
            ];
            if ((string) $item['type'] === 'C') {
                $customers[$user_id]['cart_products'][] = $product;
            } else {
                $customers[$user_id]['wishlist_products'][] = $product;
            }
            $per_user_session_products[$user_id] = $count + 1;
        }

        $emails = array_values(array_unique(array_filter(array_map(
            static function (array $customer): string {
                return trim((string) $customer['email']);
            },
            $customers
        ))));

        if ($emails) {
            $subscriptions_by_email = [];
            foreach (db_get_array(
                'SELECT s.email, uml.list_id, uml.confirmed, uml.timestamp,'
                . ' ml.status AS list_status, ml.register_autoresponder'
                . ' FROM ?:subscribers s'
                . ' INNER JOIN ?:user_mailing_lists uml ON uml.subscriber_id = s.subscriber_id'
                . ' INNER JOIN ?:mailing_lists ml ON ml.list_id = uml.list_id'
                . ' WHERE s.email IN (?a)'
                . ' ORDER BY s.email ASC, uml.list_id ASC',
                $emails
            ) as $subscription) {
                $email_key = strtolower(trim((string) $subscription['email']));
                $subscriptions_by_email[$email_key][] = [
                    'list_id' => (int) $subscription['list_id'],
                    'confirmed' => (int) $subscription['confirmed'] === 1,
                    'list_status' => (string) $subscription['list_status'],
                    'register_autoresponder' => (string) $subscription['register_autoresponder'],
                    'timestamp' => (int) $subscription['timestamp'],
                ];
            }

            foreach ($customers as &$customer) {
                $email_key = strtolower(trim((string) $customer['email']));
                $customer['newsletter_subscriptions'] = $subscriptions_by_email[$email_key] ?? [];
            }
            unset($customer);
        }
    }

    fn_log_event('general', 'runtime', [
        'message' => 'Talario CRM customer-360 authorized request completed',
        'mode' => 'crm',
        'customer_count' => count($customers),
        'source_ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
    ]);

    fn_talario_analytics_json_response(200, [
        'schema_version' => 'talario.crm.customer-360.v1',
        'generated_at' => (new DateTimeImmutable('now', $timezone))->format(DateTimeInterface::ATOM),
        'registered_from' => $registered_from_raw !== '' ? $registered_from_raw : null,
        'limit' => $limit,
        'has_more' => $has_more,
        'next_user_id' => $has_more && $rows ? (int) $rows[count($rows) - 1]['user_id'] : null,
        'customers' => array_values($customers),
    ]);
}
