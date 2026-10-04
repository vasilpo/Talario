<?php

defined('BOOTSTRAP') or die('Access denied');

function fn_talario_partner_sync_prod_response(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fn_talario_partner_sync_prod_policy(): array
{
    $path = DIR_ROOT . '/app/addons/talario_analytics/config/partner_sync_prod_policy.json';
    $stat = @lstat($path);
    if (!is_array($stat)
        || !is_file($path)
        || is_link($path)
        || (($stat['mode'] & 0022) !== 0)
        || (int) ($stat['size'] ?? 0) <= 0
        || (int) ($stat['size'] ?? 0) > 4096
    ) {
        return [];
    }
    $raw = file_get_contents($path);
    $policy = is_string($raw) ? json_decode($raw, true) : null;
    if (!is_array($policy)
        || json_last_error() !== JSON_ERROR_NONE
        || (string) ($policy['schema_version'] ?? '') !== 'talario.partner-sync.prod-policy.v1'
        || ($policy['enabled'] ?? false) !== true
        || ($policy['create_only'] ?? false) !== true
        || (string) ($policy['forced_status'] ?? '') !== 'H'
    ) {
        return [];
    }
    return $policy;
}

function fn_talario_partner_sync_prod_verify_signature(string $raw_body): string
{
    $request_id = trim((string) ($_SERVER['HTTP_X_TALARIO_REQUEST_ID'] ?? ''));
    $timestamp_raw = trim((string) ($_SERVER['HTTP_X_TALARIO_TIMESTAMP'] ?? ''));
    $signature_b64 = trim((string) ($_SERVER['HTTP_X_TALARIO_SIGNATURE'] ?? ''));

    if (!preg_match('/^part-sync-prod-apply-[A-Za-z0-9._:-]{6,96}$/', $request_id)) {
        fn_talario_partner_sync_prod_response(403, ['error' => 'request_not_allowed']);
    }
    if (!preg_match('/^[0-9]{10}$/', $timestamp_raw)) {
        fn_talario_partner_sync_prod_response(400, ['error' => 'timestamp_invalid']);
    }
    if (abs(time() - (int) $timestamp_raw) > 120) {
        fn_talario_partner_sync_prod_response(403, ['error' => 'request_expired']);
    }
    if ($signature_b64 === '' || strlen($signature_b64) > 8192) {
        fn_talario_partner_sync_prod_response(400, ['error' => 'signature_invalid']);
    }

    $signature = base64_decode($signature_b64, true);
    if (!is_string($signature)
        || strlen($signature) < 128
        || strlen($signature) > 4096
        || strpos($signature, '-----BEGIN SSH SIGNATURE-----') !== 0
    ) {
        fn_talario_partner_sync_prod_response(400, ['error' => 'signature_invalid']);
    }

    $ssh_keygen = '/usr/bin/ssh-keygen';
    $ssh_stat = @stat($ssh_keygen);
    if (!is_array($ssh_stat)
        || !is_executable($ssh_keygen)
        || (int) $ssh_stat['uid'] !== 0
        || (($ssh_stat['mode'] & 0022) !== 0)
        || (($ssh_stat['mode'] & 06000) !== 0)
    ) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_runtime_unavailable']);
    }

    $manifest_path = DIR_ROOT . '/app/addons/talario_analytics/config/partner_sync_signers.json';
    $manifest_stat = @lstat($manifest_path);
    if (!is_array($manifest_stat)
        || !is_file($manifest_path)
        || is_link($manifest_path)
        || (($manifest_stat['mode'] & 0022) !== 0)
        || (int) ($manifest_stat['size'] ?? 0) <= 0
        || (int) ($manifest_stat['size'] ?? 0) > 16384
    ) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_unavailable']);
    }
    $manifest_raw = file_get_contents($manifest_path);
    $manifest = is_string($manifest_raw) ? json_decode($manifest_raw, true) : null;
    if (!is_array($manifest)
        || json_last_error() !== JSON_ERROR_NONE
        || (string) ($manifest['schema_version'] ?? '') !== 'talario.partner-sync.signers.v1'
        || !isset($manifest['signers'])
        || !is_array($manifest['signers'])
        || !$manifest['signers']
        || count($manifest['signers']) > 4
    ) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_invalid']);
    }

    $trusted = [];
    $ids = [];
    foreach ($manifest['signers'] as $signer) {
        if (!is_array($signer)) {
            fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_invalid']);
        }
        $id = trim((string) ($signer['id'] ?? ''));
        $algorithm = trim((string) ($signer['algorithm'] ?? ''));
        $public_key = trim((string) ($signer['public_key'] ?? ''));
        $status = trim((string) ($signer['status'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9._:-]{3,64}$/', $id)
            || isset($ids[$id])
            || $algorithm !== 'ssh-ed25519'
            || !in_array($status, ['active', 'next'], true)
            || !preg_match('/^[A-Za-z0-9+\/]+={0,3}$/', $public_key)
        ) {
            fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_invalid']);
        }
        $decoded = base64_decode($public_key, true);
        if (!is_string($decoded) || strlen($decoded) < 32 || strlen($decoded) > 256) {
            fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_invalid']);
        }
        $ids[$id] = true;
        $trusted[$algorithm . ' ' . $public_key] = true;
    }
    if (count($trusted) !== count($manifest['signers'])) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signer_manifest_invalid']);
    }

    try {
        $nonce = bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_random_failed']);
    }
    $tmp_base = rtrim((string) sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    $tmp_dir = $tmp_base . DIRECTORY_SEPARATOR . 'talario-part-sync-prod-' . $nonce;
    if (!@mkdir($tmp_dir, 0700, false)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_temp_unavailable']);
    }
    @chmod($tmp_dir, 0700);
    $tmp_stat = @lstat($tmp_dir);
    if (!is_array($tmp_stat)
        || is_link($tmp_dir)
        || realpath($tmp_dir) !== $tmp_dir
        || (($tmp_stat['mode'] & 0777) !== 0700)
    ) {
        @rmdir($tmp_dir);
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_temp_untrusted']);
    }

    $allowed_file = $tmp_dir . '/allow';
    $signature_file = $tmp_dir . '/sig';
    $cleanup = static function () use ($allowed_file, $signature_file, $tmp_dir): void {
        @unlink($allowed_file);
        @unlink($signature_file);
        if (is_dir($tmp_dir) && !is_link($tmp_dir)) {
            @rmdir($tmp_dir);
        }
    };

    $allowed = '';
    foreach (array_keys($trusted) as $public) {
        $allowed .= 'github-actions-talario-prod ' . $public . PHP_EOL;
    }
    $ok = file_put_contents($allowed_file, $allowed, LOCK_EX) === strlen($allowed)
        && file_put_contents($signature_file, $signature, LOCK_EX) === strlen($signature);
    @chmod($allowed_file, 0600);
    @chmod($signature_file, 0600);
    if (!$ok) {
        $cleanup();
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_temp_write_failed']);
    }

    $message = "talario-part-sync-prod\napply\n"
        . $request_id . "\n"
        . $timestamp_raw . "\n"
        . hash('sha256', $raw_body) . "\n";

    $process = proc_open(
        [
            $ssh_keygen,
            '-Y', 'verify',
            '-f', $allowed_file,
            '-I', 'github-actions-talario-prod',
            '-n', 'talario-part-sync-prod',
            '-s', $signature_file,
        ],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        DIR_ROOT,
        ['PATH' => '/usr/bin:/bin']
    );
    if (!is_resource($process)) {
        $cleanup();
        fn_talario_partner_sync_prod_response(503, ['error' => 'signature_verifier_failed']);
    }
    fwrite($pipes[0], $message);
    fclose($pipes[0]);
    stream_get_contents($pipes[1], 4096);
    stream_get_contents($pipes[2], 4096);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $rc = proc_close($process);
    $cleanup();
    if ($rc !== 0) {
        fn_talario_partner_sync_prod_response(403, ['error' => 'signature_rejected']);
    }

    return $request_id;
}

function fn_talario_partner_sync_prod_replay_guard(string $request_id): void
{
    $cache_root = DIR_ROOT . '/var/cache';
    $cache_real = realpath($cache_root);
    if ($cache_real === false || str_replace('\\', '/', $cache_real) !== str_replace('\\', '/', $cache_root)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'replay_guard_unavailable']);
    }
    $dir = $cache_real . '/talario-part-sync-prod-replay';
    if (!is_dir($dir) && !@mkdir($dir, 0700, false)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'replay_guard_unavailable']);
    }
    @chmod($dir, 0700);
    $dir_stat = @lstat($dir);
    if (!is_array($dir_stat) || is_link($dir) || (($dir_stat['mode'] & 0777) !== 0700)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'replay_guard_untrusted']);
    }

    $now = time();
    $scanned = 0;
    foreach ((array) glob($dir . '/*.used') as $old) {
        if (++$scanned > 100) {
            break;
        }
        if (is_file($old) && !is_link($old) && ($now - (int) @filemtime($old)) > 600) {
            @unlink($old);
        }
    }

    $marker = $dir . '/' . hash('sha256', $request_id) . '.used';
    $handle = @fopen($marker, 'x+b');
    if (!is_resource($handle)) {
        fn_talario_partner_sync_prod_response(409, ['error' => 'request_replayed']);
    }
    @chmod($marker, 0600);
    fwrite($handle, (string) $now);
    fflush($handle);
    fclose($handle);
}

function fn_talario_partner_sync_prod_run_cli(string $raw, int $approved_company_id): void
{
    $php_real = false;
    foreach (['/usr/local/bin/php8.2', '/usr/bin/php8.2', '/usr/local/php82/bin/php'] as $candidate) {
        $real = realpath($candidate);
        $stat = $real !== false ? @stat($real) : false;
        if ($real !== false
            && is_array($stat)
            && is_file($real)
            && is_executable($real)
            && (int) $stat['uid'] === 0
            && (($stat['mode'] & 0022) === 0)
            && (($stat['mode'] & 06000) === 0)
        ) {
            $php_real = $real;
            break;
        }
    }
    if ($php_real === false) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_runtime_unavailable']);
    }

    $owner_reference = DIR_ROOT . '/init.php';
    $owner_stat = @stat($owner_reference);
    $runner_path = DIR_ROOT . '/ops/partner-sync-prod-apply.php';
    $runner_stat = @stat($runner_path);
    if (!is_array($owner_stat)
        || !is_array($runner_stat)
        || !is_file($runner_path)
        || is_link($runner_path)
        || (int) $runner_stat['uid'] !== (int) $owner_stat['uid']
        || (($runner_stat['mode'] & 0022) !== 0)
    ) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_runner_untrusted']);
    }
    $expected_runner_sha256 = 'cb788f084aad17278ca7994b043ca327b7879b2f73f72b07d222192bb3622f63';
    $actual_runner_sha256 = hash_file('sha256', $runner_path);
    if (!is_string($actual_runner_sha256) || !hash_equals($expected_runner_sha256, $actual_runner_sha256)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_runner_integrity_failed']);
    }

    $timeout = '/usr/bin/timeout';
    $timeout_stat = @stat($timeout);
    if (!is_array($timeout_stat)
        || !is_file($timeout)
        || is_link($timeout)
        || !is_executable($timeout)
        || (int) $timeout_stat['uid'] !== 0
        || (($timeout_stat['mode'] & 0022) !== 0)
        || (($timeout_stat['mode'] & 06000) !== 0)
    ) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_timeout_unavailable']);
    }

    $process = proc_open(
        [
            $timeout,
            '--signal=TERM',
            '--kill-after=5s',
            '60s',
            $php_real,
            '-d', 'display_errors=0',
            '-d', 'html_errors=0',
            $runner_path,
        ],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        DIR_ROOT,
        [
            'PATH' => '/usr/bin:/bin',
            'TALARIO_PARTNER_SYNC_ROOT' => DIR_ROOT,
            'TALARIO_PARTNER_SYNC_RUNNER_UID' => (string) ((int) $runner_stat['uid']),
            'TALARIO_PARTNER_SYNC_APPROVED_COMPANY_ID' => (string) $approved_company_id,
        ]
    );
    if (!is_resource($process)) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_start_failed']);
    }
    $written = fwrite($pipes[0], $raw);
    fclose($pipes[0]);
    if ($written !== strlen($raw)) {
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_terminate($process);
        proc_close($process);
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_input_failed']);
    }
    $stdout = stream_get_contents($pipes[1], 1048576);
    stream_get_contents($pipes[2], 4096);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $rc = proc_close($process);

    if (in_array((int) $rc, [124, 137], true)) {
        fn_talario_partner_sync_prod_response(504, ['error' => 'cli_timeout']);
    }
    if (!is_string($stdout) || strlen($stdout) > 1048576) {
        fn_talario_partner_sync_prod_response(503, ['error' => 'cli_output_invalid']);
    }
    $payload = json_decode(trim($stdout), true);
    if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
        fn_talario_partner_sync_prod_response(500, ['error' => 'cli_invalid_response', 'runner_rc' => (int) $rc]);
    }
    $status = isset($payload['http_status']) ? (int) $payload['http_status'] : ($rc === 0 ? 200 : 500);
    unset($payload['http_status']);
    if ((int) $rc === 0) {
        $status = in_array($status, [200, 201], true) ? $status : 200;
    } else {
        $status = $status >= 400 && $status <= 599 ? $status : 500;
    }
    fn_talario_partner_sync_prod_response($status, $payload);
}

if ($mode !== 'apply') {
    fn_talario_partner_sync_prod_response(404, ['error' => 'not_found']);
}
if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
    fn_talario_partner_sync_prod_response(405, ['error' => 'method_not_allowed']);
}
if (function_exists('fn_is_development') && fn_is_development()) {
    fn_talario_partner_sync_prod_response(404, ['error' => 'not_found']);
}
if (!fn_talario_partner_sync_prod_policy()) {
    fn_talario_partner_sync_prod_response(404, ['error' => 'not_found']);
}

$max_payload_bytes = 20971520;
$content_length = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
if ($content_length > $max_payload_bytes) {
    fn_talario_partner_sync_prod_response(413, ['error' => 'payload_too_large']);
}
$raw = file_get_contents('php://input', false, null, 0, $max_payload_bytes + 1);
if (!is_string($raw) || $raw === '' || strlen($raw) > $max_payload_bytes) {
    fn_talario_partner_sync_prod_response(400, ['error' => 'invalid_payload']);
}
$payload = json_decode($raw, true);
if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
    fn_talario_partner_sync_prod_response(400, ['error' => 'invalid_json']);
}
if ((string) ($payload['operation'] ?? '') !== 'create' || array_key_exists('product_id', $payload)) {
    fn_talario_partner_sync_prod_response(403, ['error' => 'prod_create_only']);
}
$approved_company_id = (int) ($payload['approved_company_id'] ?? 0);
$product = isset($payload['product']) && is_array($payload['product']) ? $payload['product'] : [];
if ($approved_company_id <= 0 || (int) ($product['company_id'] ?? 0) !== $approved_company_id) {
    fn_talario_partner_sync_prod_response(403, ['error' => 'company_run_approval_required']);
}
if (isset($product['status']) && (string) $product['status'] !== 'H') {
    fn_talario_partner_sync_prod_response(403, ['error' => 'prod_hidden_only']);
}
$dry_run = !array_key_exists('dry_run', $payload) || (bool) $payload['dry_run'];
if (!$dry_run) {
    $approval_id = trim((string) ($payload['approval_id'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9._:-]{6,128}$/', $approval_id)) {
        fn_talario_partner_sync_prod_response(400, ['error' => 'approval_id_required']);
    }
}

$request_id = fn_talario_partner_sync_prod_verify_signature($raw);
fn_talario_partner_sync_prod_replay_guard($request_id);

$payload['product']['status'] = 'H';
$normalized = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_string($normalized) || $normalized === '') {
    fn_talario_partner_sync_prod_response(500, ['error' => 'payload_normalization_failed']);
}
fn_talario_partner_sync_prod_run_cli($normalized, $approved_company_id);
