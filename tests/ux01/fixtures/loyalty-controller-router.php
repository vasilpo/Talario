<?php
/**
 * Synthetic localhost router for loyalty-controller.test.cjs.
 * Never bootstraps CS-Cart, reads a database, or accepts an account ID.
 */
namespace Tygh {
    final class Registry
    {
        public static $values = [];

        public static function get($key)
        {
            return self::$values[$key] ?? null;
        }
    }
}

namespace {
    // A deployed copy must not become a second application entry point.
    if (PHP_SAPI !== 'cli-server' || ($_SERVER['REMOTE_ADDR'] ?? '') !== '127.0.0.1') {
        http_response_code(404);
        exit;
    }

    $cases = [
        'positive' => ['points' => 145],
        'zero' => ['points' => 0],
        'missing-row' => ['points' => false],
        'empty-row' => ['points' => ''],
        'null-row' => ['points' => null],
        'numeric-string' => ['points' => '57'],
        'spoofed-id' => ['points' => 145, 'request' => ['user_id' => 9001, 'userId' => 9001, 'points' => 999999]],
        'guest' => ['points' => 145, 'auth' => []],
        'guest-spoofed-id' => ['points' => 145, 'auth' => [], 'request' => ['user_id' => 42, 'userId' => 42]],
        'missing-constant' => ['points' => 145, 'constant' => false],
        'missing-reader' => ['points' => 145, 'reader' => false],
        'missing-both' => ['points' => 145, 'constant' => false, 'reader' => false],
        // Native fn_change_user_points permits signed balances after adjustments.
        'signed-negative' => ['points' => -100],
        'signed-negative-string' => ['points' => '-100'],
        'invalid-string' => ['points' => 'not-a-balance'],
        'invalid-array' => ['points' => ['points' => 145]],
        'rewrite' => ['points' => 145, 'script' => '/index.php', 'uri' => '/dev_copy/?dispatch=ux01_loyalty.balance'],
        'wrong-area' => ['points' => 145, 'area' => 'A'],
        'wrong-mode' => ['points' => 145, 'mode' => 'view'],
        'prod-uri' => ['points' => 145, 'uri' => '/index.php?dispatch=ux01_loyalty.balance'],
        'outside-config' => ['points' => 145, 'script' => '/index.php', 'current_path' => '/'],
    ];

    $case_name = $_GET['fixture'] ?? '';
    if (!isset($cases[$case_name])) {
        http_response_code(404);
        exit;
    }
    $case = $cases[$case_name];

    define('BOOTSTRAP', true);
    define('AREA', $case['area'] ?? 'C');
    define('CONTROLLER_STATUS_NO_PAGE', 404);
    if (($case['constant'] ?? true) === true) {
        // Native Reward Points config.php defines POINTS as W.
        define('POINTS', 'W');
    }
    if (($case['reader'] ?? true) === true) {
        function fn_get_user_additional_data($type, $user_id = 0)
        {
            // An observation header lets the test assert the exact read identity.
            header('X-Fixture-Reward-Read: ' . json_encode([$type, $user_id]));
            return $GLOBALS['ux01_fixture_points'];
        }
    }

    $GLOBALS['ux01_fixture_points'] = $case['points'];
    \Tygh\Registry::$values['config.current_path'] = $case['current_path'] ?? '/dev_copy';
    $_SERVER['SCRIPT_NAME'] = $case['script'] ?? '/dev_copy/index.php';
    $_SERVER['REQUEST_URI'] = $case['uri'] ?? '/dev_copy/index.php?dispatch=ux01_loyalty.balance';
    $_REQUEST = $_GET = $_POST = $case['request'] ?? [];
    $auth = $case['auth'] ?? ['user_id' => 42];
    $mode = $case['mode'] ?? 'balance';

    // Execute the unchanged application controller, including its header()/exit.
    $result = require dirname(__DIR__, 3) . '/app/addons/lr_design_changes/controllers/frontend/ux01_loyalty.php';

    // Only a rejected area/path/mode returns rather than exiting with a balance.
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['controller_status' => $result]);
}
