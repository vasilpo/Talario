<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
// The normal UX01 PHP stage runs after setup-php; do not silently skip HTTP tests.
$command = 'PHP_BINARY=' . escapeshellarg(PHP_BINARY)
    . ' node --test ' . escapeshellarg(__DIR__ . '/loyalty-controller.test.cjs');
passthru($command, $status);
if ($status !== 0) {
    exit($status);
}
