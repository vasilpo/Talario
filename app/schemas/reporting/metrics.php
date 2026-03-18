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
use Tygh\Themes\Styles;
use Tygh\Themes\Themes;
use Tygh\BlockManager\Layout;

/**
 * Describes the metrics used in store functionality reports.
 * It helps to analyze the use of the released functionality.
 *
 * Syntax:
 * 'metric_name' => true|false|callable
 *
 * 'metric_name' - the unique name of the metric
 */

return array(
    'theme_editor' => function () {
        $company_ids = fn_allowed_for('ULTIMATE') ? fn_get_available_company_ids() : array(0);

        foreach ($company_ids as $company_id) {
            $layout = Layout::instance($company_id)->getDefault();

            if (Styles::factory($layout['theme_name'])->getDefault() !== $layout['style_id']) {
                return true;
            }
        }

        return false;
    },
    'parent_themes' => function () {
        $company_ids = fn_allowed_for('ULTIMATE') ? fn_get_available_company_ids() : array(0);

        foreach ($company_ids as $company_id) {
            $theme_name = fn_get_theme_path('[theme]', 'C', $company_id);

            if (Themes::factory($theme_name)->getParent() !== null) {
                return true;
            }
        }

        return false;
    },
    'promotions_on_order_update' => !Registry::ifGet('config.tweaks.do_not_apply_promotions_on_order_update', false),
    'boxbery' => false,
    'ebay' => false,
    'variations' => false,
    'retailcrm' => false,
    'atol_online' => false,
    'advanced_import' => false,
    'responsive_admin' => true,
    'gdpr' => false,
    'step_by_step_checkout' => false,
);