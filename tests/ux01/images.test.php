<?php
namespace Tygh {
    class Registry {
        public static $path = '/dev_copy';
        public static function get($key) { return $key === 'config.current_path' ? self::$path : null; }
    }
}
namespace {
    define('BOOTSTRAP', true);
    define('AREA', 'C');
    require_once __DIR__ . '/../../app/addons/lr_design_changes/func.php';
    $source = ['detailed' => ['relative_path' => 'detailed/2/photo test.jpg', 'alt' => 'Real image', 'image_x' => 800, 'image_y' => 600]];
    function expectImage($condition, $message) { if (!$condition) { throw new \RuntimeException($message); } }
    $image = [];
    fn_lr_design_changes_image_to_display_post($image, $source, 148, 130);
    expectImage(($image['image_path'] ?? '') === '/images/detailed/2/photo%20test.jpg', 'Missing dev_copy thumbnail must use its same-origin public original');
    expectImage(($image['width'] ?? 0) === 148 && ($image['height'] ?? 0) === 130, 'Requested geometry preserved');
    $native = ['image_path' => '/native-thumbnail.jpg'];
    fn_lr_design_changes_image_to_display_post($native, $source, 148, 130);
    expectImage($native === ['image_path' => '/native-thumbnail.jpg'], 'Existing native thumbnail must remain unchanged');
    foreach (['', '/other', '/dev_copy_other'] as $path) {
        \Tygh\Registry::$path = $path;
        $image = [];
        fn_lr_design_changes_image_to_display_post($image, $source, 148, 130);
        expectImage($image === [], 'Fallback must be dev_copy-only');
    }
    \Tygh\Registry::$path = '/dev_copy';
    foreach (['../secret', 'detailed/2/../secret.jpg', 'https://example.org/image.jpg', 'detailed/2/file.php'] as $path) {
        $image = [];
        $bad = ['detailed' => ['relative_path' => $path]];
        fn_lr_design_changes_image_to_display_post($image, $bad, 148, 130);
        expectImage($image === [], 'Unsafe image path rejected');
    }
    echo 'PASS dev_copy original-image fallback, native preservation, PROD exclusion and path safety' . PHP_EOL;
}
