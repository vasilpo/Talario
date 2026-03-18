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

use Tygh\Enum\YesNo;
use Tygh\Providers\StorefrontProvider;
use Tygh\Settings;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

/** @var string $mode Controller mode */
if ($mode === 'manage') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $section_id = (string) $view->getTemplateVars('section_id');
    if ($section_id !== 'Appearance') {
        return [CONTROLLER_STATUS_OK];
    }

    $single_url_setting_values = fn_allowed_for('ULTIMATE')
        ? (array) Settings::instance()->getAllVendorsValues('single_url', 'seo')
        : [Settings::instance()->getValue('single_url', 'seo')];
    if (!in_array(YesNo::YES, $single_url_setting_values, true)) {
        return [CONTROLLER_STATUS_OK];
    }

    $is_default_storefront_affected = false;
    $selected_storefront_id = (int) $view->getTemplateVars('selected_storefront_id');
    if ($selected_storefront_id === 0) {
        $is_default_storefront_affected = true;
    } else {
        $storefront = StorefrontProvider::getRepository()->findDefault();
        $is_default_storefront_affected = $storefront && $storefront->storefront_id === $selected_storefront_id;
    }

    $view->assign(
        [
            'show_language_warning'          => true,
            'is_default_storefront_affected' => $is_default_storefront_affected,
        ]
    );
}

return [CONTROLLER_STATUS_OK];
