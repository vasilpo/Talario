<?php
/**
 * UX-01: fetch the signed-in customer's own reward-point balance.
 *
 * Read-only endpoint, dev_copy only. Do not put per-user points into cached
 * homepage HTML or expose any identity or balance to a guest.
 */
defined('BOOTSTRAP') or die('Access denied');

if ($mode !== 'balance') {
    return [CONTROLLER_STATUS_NO_PAGE];
}

// Require the dev storefront. The configured public path is more reliable
// than SCRIPT_NAME on installations with a front-controller rewrite.
$script_path = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$current_path = (string) (\Tygh\Registry::get('config.current_path') ?: '');
$request_path = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '');
$is_dev_request = (bool) preg_match('~^/dev_copy/(?:index\\.php)?$~', $request_path);
$is_dev_script = (bool) preg_match('~^/dev_copy/(?:index\\.php)?$~', $script_path);
$is_dev_storefront = rtrim($current_path, '/') === '/dev_copy';
if (AREA !== 'C' || !$is_dev_request || (!$is_dev_script && !$is_dev_storefront)) {
    return [CONTROLLER_STATUS_NO_PAGE];
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-store, max-age=0');
header('Pragma: no-cache');
header('Vary: Cookie', false);
header('X-Content-Type-Options: nosniff');

$user_id = isset($auth['user_id']) ? (int) $auth['user_id'] : 0;
$response = ['authenticated' => $user_id > 0, 'points' => null];

if ($user_id > 0 && defined('POINTS') && function_exists('fn_get_user_additional_data')) {
    $raw_points = fn_get_user_additional_data(POINTS, $user_id);
    // CS-Cart's reward_points_user_init treats an absent user_data row as zero.
    // Match that behavior: a signed-in customer with no points must see 0,
    // not a silently missing account-balance widget.
    if (empty($raw_points)) {
        $raw_points = 0;
    }
    // Reward Points permits signed balances (for example after an adjustment).
    // Return the real amount; a negative balance is not an unavailable balance.
    if (is_numeric($raw_points)) {
        $response['points'] = (int) $raw_points;
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
exit;
