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

use Tygh\Enum\SettingTypes;
use Tygh\Licensing\Features;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'manage' && !fn_is_allowed(Features::PRODUCT_VIDEOS)) {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $options = (array) $view->getTemplateVars('options');

    foreach ($options as $section_key => &$settings) {
        $last_index = 0;

        foreach ($settings as $setting_id => &$setting) {
            if (
                !in_array($setting['name'], ['global_show_videos_before_images', 'default_show_videos_before_images', 'global_autoplay_videos', 'default_autoplay_videos'])
            ) {
                continue;
            }

            $setting['options']['input_attributes']['disabled'] = 'disabled';
            $last_index = $setting_id;
        }
        unset($setting);

        if ($last_index) {
            $original_vars = $view->getTemplateVars();
            $view->assign([
                'feature'          => Features::PRODUCT_VIDEOS,
                'text'             => __('upsell.product_videos.settings'),
                'is_control_group' => false,
            ]);
            $tooltip = $view->fetch('design/backend/templates/addons/upsell/components/tooltip.tpl');
            $view->assign($original_vars);

            $upsell_video = [
                'object_id' => 0,
                'name'      => 'upsell_videos',
                'type'      => SettingTypes::INFO,
                'info'      => $tooltip
            ];

            $changed_options = [];
            foreach ($settings as $setting_id => $setting) {
                $changed_options[$setting_id] = $setting;
                if ($setting_id === $last_index) {
                    $changed_options[0] = $upsell_video;
                }
            }
            $options[$section_key] = $changed_options;
        }
    }

    Tygh::$app['view']->assign('options', $options);
}

return [CONTROLLER_STATUS_OK];
