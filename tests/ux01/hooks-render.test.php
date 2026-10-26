<?php
/** Exercise the real hooks with the repository's Smarty engine; no CS-Cart/DB boot. */
require_once __DIR__ . '/../../app/lib/vendor/smarty/smarty/libs/Smarty.class.php';

$root = dirname(__DIR__, 2);
$cache = sys_get_temp_dir() . '/talario-ux01-smarty-' . getmypid();
mkdir($cache, 0700, true);
$smarty = new Smarty\Smarty();
$smarty->setCompileDir($cache);
$smarty->setTemplateDir($root . '/design/themes/abt__unitheme2/templates');
$smarty->registerPlugin('function', 'script', static function ($params) {
    return '<script src="' . htmlspecialchars($params['src'], ENT_QUOTES, 'UTF-8') . '"></script>';
});
$smarty->registerPlugin('function', 'style', static function ($params) {
    return '<link href="' . htmlspecialchars($params['src'], ENT_QUOTES, 'UTF-8') . '">';
});

function check($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

try {
    // categories.view was observed on the real homepage after native catalog rendering.
    foreach (['index', 'categories', ''] as $controller) {
        $smarty->assign('config', ['current_path' => '/dev_copy']);
        $smarty->assign('runtime', ['controller' => $controller, 'mode' => 'view']);
        $smarty->assign('auth', ['user_id' => 1]);
        $smarty->assign('user_info', ['firstname' => 'Test"><img src=x onerror=alert(1)>']);
        $scripts = $smarty->fetch('addons/lr_design_changes/hooks/index/scripts.post.tpl');
        $styles = $smarty->fetch('addons/lr_design_changes/hooks/index/styles.post.tpl');
        check(strpos($scripts, 'ux01_homepage.js') !== false, 'UX-01 JS missing for dev_copy runtime ' . $controller);
        check(strpos($styles, 'ux01_homepage.less') !== false, 'UX-01 CSS missing for dev_copy runtime ' . $controller);
        check(strpos($scripts, '<img') === false, 'First name must be HTML escaped');
        check(strpos($scripts, '&lt;img') !== false, 'Escaped first name context missing');
        echo 'PASS dev_copy assets + escaped context: ' . ($controller ?: '(empty)') . PHP_EOL;
    }
    foreach (['', '/shop', '/dev_copy_other'] as $path) {
        $smarty->assign('config', ['current_path' => $path]);
        $smarty->assign('runtime', ['controller' => 'index', 'mode' => 'index']);
        $scripts = $smarty->fetch('addons/lr_design_changes/hooks/index/scripts.post.tpl');
        $styles = $smarty->fetch('addons/lr_design_changes/hooks/index/styles.post.tpl');
        check(strpos($scripts . $styles, 'ux01') === false, 'UX-01 must not leak outside /dev_copy: ' . $path);
        check(strpos($scripts, 'homepage_catalog.js') !== false, 'Native catalog JS removed');
        check(strpos($scripts, 'homepage_search_filters.js') !== false, 'Native filter JS removed');
        check(strpos($styles, 'addons/lr_design_changes/styles.less') !== false, 'Native styles removed');
        echo 'PASS native assets preserved; UX-01 absent: ' . ($path ?: 'PROD root') . PHP_EOL;
    }
} finally {
    $smarty->clearCompiledTemplate();
    rmdir($cache);
}
