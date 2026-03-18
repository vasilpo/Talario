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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (in_array($mode, ['update', 'add'], true)) {
    Tygh::$app['view']->assign('zero_company_id_name_lang_var', __('marketplace'));
}

if ($mode === 'manage') {
    $company_name = Registry::get('settings.Company.company_name');
    Tygh::$app['view']->assign('marketplace_company_name', $company_name);
    Tygh::$app['view']->assign('marketplace_store_location_name', __('marketplace'));
}
