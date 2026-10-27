<?php
/** The first response must contain UX-01 chrome, with a bounded native fallback. */
require_once __DIR__ . '/../../app/lib/vendor/smarty/smarty/libs/Smarty.class.php';
$smarty = new Smarty\Smarty();
$cache = sys_get_temp_dir() . '/talario-ux01-first-render-' . getmypid();
mkdir($cache, 0700, true);
$smarty->setCompileDir($cache);
$smarty->setTemplateDir(dirname(__DIR__, 2) . '/design/themes/abt__unitheme2/templates');
function requireFirstRender($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
try {
    $smarty->assign('auth', ['user_id' => 1]);
    $smarty->assign('user_info', ['firstname' => 'Test"><img src=x onerror=alert(1)>']);
    foreach ([['/dev_copy', 'index', 'index', true], ['', 'index', 'index', false], ['/dev_copy_other', 'index', 'index', false], ['/dev_copy', 'products', 'view', false]] as $case) {
        [$path, $controller, $mode, $expected] = $case;
        $smarty->assign('config', ['current_path' => $path]);
        $smarty->assign('runtime', ['controller' => $controller, 'mode' => $mode]);
        $head = $smarty->fetch('addons/lr_design_changes/hooks/index/head_scripts.pre.tpl');
        $body = $smarty->fetch('addons/lr_design_changes/hooks/index/content.pre.tpl');
        if ($expected) {
            requireFirstRender(strpos($body, 'id="talario-ux01"') !== false && strpos($body, 'Занятия для детей') !== false, 'First HTML is missing the approved UX-01 heading');
            requireFirstRender(strpos($body, 'talario-ux01-logo.png') !== false, 'First HTML is missing the existing approved logo');
            requireFirstRender(strpos($body, '<img src=x') === false, 'Server-rendered first name is not escaped');
            requireFirstRender(strpos($head, 'data-no-defer') !== false && strpos($head, 'setTimeout') !== false, 'First-paint guard must run in the head and release native fallback without jQuery');
            requireFirstRender(strpos($body, 'role="status"') !== false, 'Catalog waiting state must be announced');
        } else {
            requireFirstRender(trim($head . $body) === '', 'First-paint presentation leaked outside dev_copy homepage');
        }
    }
    echo "PASS server first render, escaped account text, exact dev_copy homepage and bounded fallback\n";
} finally {
    $smarty->clearCompiledTemplate();
    rmdir($cache);
}
