<?php
/** A native LESS parse failure would break storefront CSS generation. */
require_once __DIR__ . '/../../app/lib/other/lessphp/lessc.inc.php';
$less = new lessc();
$css = $less->compileFile(__DIR__ . '/../../design/themes/abt__unitheme2/css/addons/lr_design_changes/ux01_homepage.less');
if ($css === '' || strpos($css, 'talario-ux01') === false) {
    throw new RuntimeException('UX-01 stylesheet did not compile');
}
echo 'PASS native CS-Cart LESS compilation (' . strlen($css) . ' bytes)' . PHP_EOL;
