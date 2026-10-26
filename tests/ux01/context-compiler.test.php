<?php
/** Exercise CS-Cart's real script extraction; storage is an in-memory boundary. */
namespace Tygh {
    class Registry {
        public static function get($key) {
            return ['config.current_location' => 'https://fixture.invalid', 'config.dir.root' => '/fixture'][$key] ?? null;
        }
    }
    class Development { public static function isEnabled($key) { return false; } }
    class Storage {
        public static function instance($key) { return new self(); }
        public function isExist($file) { return true; }
        public function getUrl($file) { return '/fixture.js'; }
    }
}
namespace {
    require_once __DIR__ . '/../../app/lib/vendor/smarty/smarty/libs/Smarty.class.php';
    require_once __DIR__ . '/../../app/Tygh/SmartyEngine/Blocks/InlineScript.php';
    require_once __DIR__ . '/../../app/Tygh/SmartyEngine/Blocks/Scripts.php';
    function fn_get_storage_data($key) { return 'fixture'; }
    class ContextTestScripts extends \Tygh\SmartyEngine\Blocks\Scripts {
        public function inlineScripts(array $params, $content, \Smarty\Template &$template, &$repeat): string { return ''; }
    }
    $smarty = new \Smarty\Smarty();
    $cache = sys_get_temp_dir() . '/talario-ux01-context-' . getmypid();
    mkdir($cache, 0700, true);
    $smarty->setCompileDir($cache);
    $smarty->registerPlugin('function', 'script', static function ($params) { return '<script src="' . $params['src'] . '"></script>'; });
    $smarty->assign('config', ['current_path' => '/dev_copy']);
    $smarty->assign('runtime', ['controller' => 'categories']);
    $smarty->assign('auth', ['user_id' => 1]);
    $smarty->assign('user_info', ['firstname' => 'Test</script><img src=x onerror=alert(1)>']);
    try {
        $template = $smarty->createTemplate(dirname(__DIR__, 2) . '/design/themes/abt__unitheme2/templates/addons/lr_design_changes/hooks/index/scripts.post.tpl');
        $rendered = $template->fetch();
        $repeat = false;
        $compiled = (new ContextTestScripts())->handle([], $rendered, $template, $repeat);
        if (strpos($compiled, 'talarioUx01Context') === false) {
            throw new \RuntimeException('Authenticated context is lost inside native Scripts compiler');
        }
        if (strpos($compiled, '<img') !== false || strpos($compiled, '\\u003C') === false) {
            throw new \RuntimeException('First name must be script-safe JSON with HEX_TAG');
        }
        echo 'PASS authenticated first-name context survives the native Scripts compiler; script injection escaped' . PHP_EOL;
    } finally {
        $smarty->clearCompiledTemplate();
        rmdir($cache);
    }
}
