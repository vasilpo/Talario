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

$script_path = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
if (AREA !== 'C' || !preg_match('~^/dev_copy/(?:index\\.php)?$~', $script_path)) {
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
    if (is_numeric($raw_points) && (float) $raw_points >= 0) {
        $response['points'] = (int) $raw_points;
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
exit;
