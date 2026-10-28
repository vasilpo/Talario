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
    // The bundled legacy compiler must retain vw/px; whitespace is flexible.
    if (!preg_match('~max-width:\s*calc\(\s*100vw\s*-\s*32px\s*\)\s*!important~', $css)) {
        throw new \RuntimeException('Native sorting menu has no compiled mobile viewport bound');
    }
    if (!preg_match('~bottom:\s*var\(--bp-bottom-panel-height,\s*0px\)\s*!important~', $css)) {
        throw new \RuntimeException('Mobile filter popup ignores the native admin preview toolbar inset');
    }
    // Legacy lessphp evaluates mixed-unit arithmetic inside unescaped calc():
    // 100% - 48px becomes 52%, squeezing the real catalog at every viewport.
    foreach ([32, 48] as $gutter) {
        if (!preg_match('~width:\s*calc\(\s*100%\s*-\s*' . $gutter . 'px\s*\)\s*!important~', $css)) {
            throw new \RuntimeException('Native LESS lost pixel gutter in catalog calc(100% - ' . $gutter . 'px)');
        }
    }
    if (!preg_match('~max-width:\\s*calc\\(\\s*100vw\\s*-\\s*64px\\s*\\)~', $css)) {
        throw new \RuntimeException('Native LESS lost the cashback tooltip pixel viewport bound');
    }
    if (!preg_match('~card-foot\\.ux01-has-cashback\\s+\\.more\\s*\\{[^}]*min-width:\\s*0(?:px)?\\s*!important~s', $css)) {
        throw new \RuntimeException('Cashback Details must override the native important minimum width');
    }
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
