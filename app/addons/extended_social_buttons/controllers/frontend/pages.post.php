<?php

use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/** @var string $mode */
if ($mode === 'view') {
    if (!empty($_REQUEST['page_id'])) {
        $settings_pr = fn_get_provider_settings();
        $display_on = Registry::get('settings.extended_social_buttons.general.social_pages.pages');

        Tygh::$app['view']->assign('display_on', $display_on);
        Tygh::$app['view']->assign('settings_pr', $settings_pr);
    }
}
