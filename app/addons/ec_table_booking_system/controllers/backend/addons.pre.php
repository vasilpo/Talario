<?php

use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update' && $_REQUEST['addon'] == 'ec_table_booking_system') {

        $ec_settings = !empty($_REQUEST['ec_settings']) ? $_REQUEST['ec_settings'] : array();
        $is_unset_opts = false; // Unset some applied settings
        $unset_types = array(); // Types of watermarks without image
        $wt_changed = array(); // array for changed watermark types

        fn_update_ec_table_booking_system_settings($ec_settings);
    }
}

if ($mode == 'update') {

    if ($_REQUEST['addon'] == 'ec_table_booking_system') {
        $ec_settings = fn_get_ec_table_booking_system_settings();

        Tygh::$app['view']->assign('ec_settings', $ec_settings);

    }

}

function fn_update_ec_table_booking_system_settings($ec_settings, $company_id = null)
{
    if (!$setting_id = Settings::instance()->getId('ec_table_booking_system', '')) {
        $setting_id = Settings::instance()->update(array(
            'name' =>           'ec_table_booking_system',
            'section_id' =>     0,
            'section_tab_id' => 0,
            'type' =>           'A', // any not existing type
            'position' =>       0,
            'is_global' =>      'N',
            'handler' =>        ''
        ));
    }
    Settings::instance()->updateValueById($setting_id, serialize($ec_settings), $company_id);
}
