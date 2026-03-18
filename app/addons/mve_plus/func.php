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

/**
 * Enables the `can_edit_blocks` and `can_edit_styles` settings if they exist in provided array
 *
 * @param array $settings Settings
 *
 * @return array
 */
function fn_mve_plus_hide_theme_and_styles_editing_settings($settings)
{
    if (isset($settings['main'])) {

        foreach ($settings['main'] as &$setting) {
            $setting_type_hidden = 'D';

            if (in_array($setting['name'], array('can_edit_blocks', 'can_edit_styles'))) {
                $setting['type'] = $setting_type_hidden;
            }
        }
        unset($setting);
    }

    return $settings;
}
