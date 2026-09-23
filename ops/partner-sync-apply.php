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

$root_stat = @stat($root);
$runner_handle = @fopen(__FILE__, 'rb');
$script_stat = is_resource($runner_handle) ? @fstat($runner_handle) : false;
if (
    !is_array($root_stat)
    || !is_array($script_stat)
    || (($script_stat['mode'] & 0170000) !== 0100000)
    || (($script_stat['mode'] & 0022) !== 0)
) {
    if (is_resource($runner_handle)) {
        fclose($runner_handle);
    }
    fwrite(STDERR, "PARTNER_SYNC_RUNNER_TRUST_FAILED\n");
    exit(4);
}
fclose($runner_handle);

$runner_uid_raw = getenv('TALARIO_PARTNER_SYNC_RUNNER_UID');
if (!is_string($runner_uid_raw) || !ctype_digit($runner_uid_raw)) {
    fwrite(STDERR, "PARTNER_SYNC_RUNNER_UID_REQUIRED\n");
    exit(4);
}
$runner_uid = (int) $runner_uid_raw;
if ((int) $script_stat['uid'] !== $runner_uid) {
    fwrite(STDERR, "PARTNER_SYNC_RUNNER_OWNER_MISMATCH\n");
    exit(4);
}

$root_uid = (int) $root_stat['uid'];
$root_mode = $root_stat['mode'] & 0777;
if (($root_stat['mode'] & 0170000) !== 0040000) {
    fwrite(STDERR, "PARTNER_SYNC_ROOT_NOT_DIRECTORY\n");
    exit(4);
}
if ($root_uid === 0) {
    if ($root_mode !== 0700) {
        fwrite(STDERR, "PARTNER_SYNC_ROOT_PERMISSIONS_UNSAFE\n");
        exit(4);
    }
} elseif ($root_uid === $runner_uid) {
    if (($root_mode & 0022) !== 0) {
        fwrite(STDERR, "PARTNER_SYNC_ROOT_PERMISSIONS_UNSAFE\n");
        exit(4);
    }
} else {
    fwrite(STDERR, "PARTNER_SYNC_ROOT_OWNER_MISMATCH\n");
    exit(4);
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
