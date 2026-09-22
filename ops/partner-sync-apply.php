<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "PARTNER_SYNC_CLI_ONLY\n");
    exit(2);
}

$requested_root = getenv('TALARIO_PARTNER_SYNC_ROOT');
if (!is_string($requested_root) || $requested_root === '' || $requested_root[0] !== '/') {
    fwrite(STDERR, "PARTNER_SYNC_DEV_COPY_ONLY\n");
    exit(3);
}

$root = realpath($requested_root);
if ($root === false) {
    fwrite(STDERR, "PARTNER_SYNC_ROOT_NOT_FOUND\n");
    exit(2);
}

$normalized_root = str_replace('\\', '/', $root);
if (!str_ends_with($normalized_root, '/talario.ru/public_html/dev_copy')) {
    fwrite(STDERR, "PARTNER_SYNC_DEV_COPY_ONLY\n");
    exit(3);
}

$script_stat = @stat(__FILE__);
if (!is_array($script_stat)) {
    fwrite(STDERR, "PARTNER_SYNC_RUNNER_STAT_FAILED\n");
    exit(4);
}

$critical_files = [
    $root . '/init.php',
    $root . '/config.local.php',
    $root . '/app/addons/talario_analytics/partner_sync_write.php',
];

foreach ($critical_files as $critical_file) {
    $critical_real = realpath($critical_file);
    $critical_stat = $critical_real !== false ? @stat($critical_real) : false;
    if (
        $critical_real === false
        || !is_array($critical_stat)
        || !is_file($critical_real)
        || $critical_stat['uid'] !== $script_stat['uid']
        || ($critical_stat['mode'] & 0022) !== 0
    ) {
        fwrite(STDERR, "PARTNER_SYNC_CRITICAL_FILE_TRUST_FAILED\n");
        exit(4);
    }
}

define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');

require $root . '/init.php';

function fn_talario_analytics_json_response(int $status, array $payload): void
{
    $payload['http_status'] = $status;
    fwrite(
        STDOUT,
        json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL
    );
    exit($status >= 400 ? 1 : 0);
}

require $root . '/app/addons/talario_analytics/partner_sync_write.php';

if (!function_exists('fn_is_development') || !fn_is_development()) {
    fn_talario_analytics_json_response(403, ['error' => 'development_runtime_required']);
}

if (!defined('TALARIO_PARTNER_SYNC_DEV_COPY') || TALARIO_PARTNER_SYNC_DEV_COPY !== true) {
    fn_talario_analytics_json_response(403, ['error' => 'dev_copy_gate_disabled']);
}

fn_talario_analytics_partner_sync_write_response();
