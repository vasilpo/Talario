<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'manage') {

    if (isset($_REQUEST['section_id']) && strtolower($_REQUEST['section_id']) == 'vendors') {
        if (!fn_allowed_for('MULTIVENDOR:PLUS,MULTIVENDOR:ULTIMATE')) {
            $options = Tygh::$app['view']->getTemplateVars('options');
            $options = fn_mve_plus_hide_theme_and_styles_editing_settings($options);
            Tygh::$app['view']->assign('options', $options);
        }
    }
}
