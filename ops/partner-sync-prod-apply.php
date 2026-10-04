<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "PARTNER_SYNC_PROD_CLI_ONLY\n");
    exit(2);
}

$requested_root = getenv('TALARIO_PARTNER_SYNC_ROOT');
if (!is_string($requested_root) || $requested_root === '' || $requested_root[0] !== '/') {
    fwrite(STDERR, "PARTNER_SYNC_PROD_ROOT_REQUIRED\n");
    exit(3);
}

$root = realpath($requested_root);
$expected_root = '/home/t/tyman5tb/talario.ru/public_html';
if ($root === false || str_replace('\\', '/', $root) !== $expected_root) {
    fwrite(STDERR, "PARTNER_SYNC_PROD_ROOT_MISMATCH\n");
    exit(3);
}

$root_stat = @stat($root);
$runner_handle = @fopen(__FILE__, 'rb');
$script_stat = is_resource($runner_handle) ? @fstat($runner_handle) : false;
if (!is_array($root_stat)
    || !is_array($script_stat)
    || (($root_stat['mode'] & 0170000) !== 0040000)
    || (($script_stat['mode'] & 0170000) !== 0100000)
    || (($script_stat['mode'] & 0022) !== 0)
) {
    if (is_resource($runner_handle)) {
        fclose($runner_handle);
    }
    fwrite(STDERR, "PARTNER_SYNC_PROD_RUNNER_TRUST_FAILED\n");
    exit(4);
}
fclose($runner_handle);

$runner_uid_raw = getenv('TALARIO_PARTNER_SYNC_RUNNER_UID');
if (!is_string($runner_uid_raw) || !ctype_digit($runner_uid_raw)) {
    fwrite(STDERR, "PARTNER_SYNC_PROD_RUNNER_UID_REQUIRED\n");
    exit(4);
}
$runner_uid = (int) $runner_uid_raw;
if ((int) $script_stat['uid'] !== $runner_uid || (int) $root_stat['uid'] !== $runner_uid) {
    fwrite(STDERR, "PARTNER_SYNC_PROD_OWNER_MISMATCH\n");
    exit(4);
}
if ((($root_stat['mode'] & 0777) & 0022) !== 0) {
    fwrite(STDERR, "PARTNER_SYNC_PROD_ROOT_PERMISSIONS_UNSAFE\n");
    exit(4);
}

$approved_company_raw = getenv('TALARIO_PARTNER_SYNC_APPROVED_COMPANY_ID');
if (!is_string($approved_company_raw) || !ctype_digit($approved_company_raw) || (int) $approved_company_raw <= 0) {
    fwrite(STDERR, "PARTNER_SYNC_PROD_COMPANY_REQUIRED\n");
    exit(4);
}
$approved_company_id = (int) $approved_company_raw;

$raw = (string) stream_get_contents(STDIN);
if ($raw === '' || strlen($raw) > 20971520) {
    fwrite(STDOUT, json_encode(['error' => 'invalid_payload', 'http_status' => 400]) . PHP_EOL);
    exit(1);
}
$payload = json_decode($raw, true);
if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDOUT, json_encode(['error' => 'invalid_json', 'http_status' => 400]) . PHP_EOL);
    exit(1);
}

if ((string) ($payload['operation'] ?? '') !== 'create' || array_key_exists('product_id', $payload)) {
    fwrite(STDOUT, json_encode(['error' => 'prod_create_only', 'http_status' => 403]) . PHP_EOL);
    exit(1);
}
if ((int) ($payload['approved_company_id'] ?? 0) !== $approved_company_id) {
    fwrite(STDOUT, json_encode(['error' => 'company_run_approval_required', 'http_status' => 403]) . PHP_EOL);
    exit(1);
}
$product = isset($payload['product']) && is_array($payload['product']) ? $payload['product'] : [];
if ((int) ($product['company_id'] ?? 0) !== $approved_company_id) {
    fwrite(STDOUT, json_encode(['error' => 'company_run_approval_required', 'http_status' => 403]) . PHP_EOL);
    exit(1);
}
if (isset($product['status']) && (string) $product['status'] !== 'H') {
    fwrite(STDOUT, json_encode(['error' => 'prod_hidden_only', 'http_status' => 403]) . PHP_EOL);
    exit(1);
}
$payload['product']['status'] = 'H';
$dry_run = !array_key_exists('dry_run', $payload) || (bool) $payload['dry_run'];
if (!$dry_run) {
    $approval_id = trim((string) ($payload['approval_id'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9._:-]{6,128}$/', $approval_id)) {
        fwrite(STDOUT, json_encode(['error' => 'approval_id_required', 'http_status' => 400]) . PHP_EOL);
        exit(1);
    }
}

// This process-local compatibility flag lets the already reviewed Partner Sync
// writer reuse its development safety checks without enabling development mode
// in the web runtime or in persistent production configuration.
if (!defined('DEVELOPMENT')) {
    define('DEVELOPMENT', true);
}
if (!defined('AREA')) {
    define('AREA', 'A');
}
if (!defined('ACCOUNT_TYPE')) {
    define('ACCOUNT_TYPE', 'admin');
}

require $root . '/init.php';

function fn_talario_analytics_json_response(int $status, array $response): void
{
    $response['http_status'] = $status;
    fwrite(STDOUT, json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    exit($status >= 400 ? 1 : 0);
}

$policy_path = $root . '/app/addons/talario_analytics/config/partner_sync_prod_policy.json';
$policy_raw = is_file($policy_path) && !is_link($policy_path) ? file_get_contents($policy_path) : false;
$policy = is_string($policy_raw) ? json_decode($policy_raw, true) : null;
if (!is_array($policy)
    || json_last_error() !== JSON_ERROR_NONE
    || (string) ($policy['schema_version'] ?? '') !== 'talario.partner-sync.prod-policy.v1'
    || ($policy['enabled'] ?? false) !== true
    || ($policy['create_only'] ?? false) !== true
    || (string) ($policy['forced_status'] ?? '') !== 'H'
) {
    fn_talario_analytics_json_response(403, ['error' => 'prod_write_policy_disabled']);
}

if (!defined('TALARIO_PARTNER_SYNC_DEV_COPY')) {
    define('TALARIO_PARTNER_SYNC_DEV_COPY', true);
}
if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE')) {
    define('TALARIO_PARTNER_SYNC_DEV_WRITE', true);
}
if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS')) {
    define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', (string) $approved_company_id);
}

$GLOBALS['TALARIO_PARTNER_SYNC_SIGNED_RAW_BODY'] = json_encode(
    $payload,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);

require $root . '/app/addons/talario_analytics/partner_sync_write.php';
fn_talario_analytics_partner_sync_write_response();
