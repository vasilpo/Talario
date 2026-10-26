<?php
namespace Tygh\Themes {
    class Themes {
        public static function areaFactory($area, $company = null, $storefront = null) { return new self(); }
        public function getThemeDirs() { return ['abt__unitheme2' => []]; }
    }
}
namespace {
    define('AREA', 'C');
    define('TIME', 1);
    function fn_normalize_path($path) { return str_replace('\\', '/', $path); }
    require_once __DIR__ . '/../../app/lib/other/lessphp/lessc.inc.php';
    require_once __DIR__ . '/../../app/Tygh/Less.php';
    $root = dirname(__DIR__, 2);
    $less = new \lessc();
    $css = $less->compileFile($root . '/design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less');
    if ($css === '' || strpos($css, 'talario-ux01') === false) { throw new \RuntimeException('UX-01 stylesheet did not compile'); }
    $cacheDir = $root . '/var/cache/misc/assets/design/themes/abt__unitheme2/css';
    $css = \Tygh\Less::parseUrls($css, $cacheDir, $root . '/design/themes/abt__unitheme2/media');
    if (!preg_match("~url\\('([^']*talario-ux01-golos\\.ttf[^']*)'\\)~", $css, $match)) { throw new \RuntimeException('Golos font URL missing'); }
    $parts = [];
    foreach (explode('/', $cacheDir . '/' . parse_url($match[1], PHP_URL_PATH)) as $part) {
        if ($part === '..') { array_pop($parts); } elseif ($part !== '' && $part !== '.') { $parts[] = $part; }
    }
    $resolved = '/' . implode('/', $parts);
    if ($resolved !== $root . '/design/themes/abt__unitheme2/media/fonts/talario-ux01-golos.ttf') {
        throw new \RuntimeException('Native CSS URL rewrite points at a missing font: ' . $resolved);
    }
    echo 'PASS native LESS compile and actual CS-Cart font URL rewriting' . PHP_EOL;
}
