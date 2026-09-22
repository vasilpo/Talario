<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "PARTNER_SYNC_CLI_ONLY\n");
    exit(2);
}

$root = realpath(dirname(__DIR__));
if (!$root) {
    fwrite(STDERR, "PARTNER_SYNC_ROOT_NOT_FOUND\n");
    exit(2);
}

$normalized_root = str_replace('\\', '/', $root);
if (substr($normalized_root, -strlen('/talario.ru/public_html/dev_copy')) !== '/talario.ru/public_html/dev_copy') {
    fwrite(STDERR, "PARTNER_SYNC_DEV_COPY_ONLY\n");
    exit(3);
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
