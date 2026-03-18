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

use Smarty\Template;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array<string, string> $params   Block params
 * @param string                $content  Block content
 * @param Template              $template Smarty template
 *
 * @return string
 */
function smarty_component_settings_settings_section(array $params, $content, Template $template)
{
    $allow_global_individual_settings = Registry::ifGet('config.tweaks.allow_global_individual_settings', false) || !empty($params['allow_global_individual_settings']);
    $items = isset($params['subsection']) ? array_filter((array) $params['subsection']) : [];

    if (!$items) {
        return '';
    }

    $settings_names = array_column($items, 'object_id', 'name');

    foreach ($items as $key => &$item) {
        if ($allow_global_individual_settings && $item['name'] === 'inventory_tracking') {
            unset($items[$key]);
            continue;
        }

        if (strpos($item['name'], 'default_') !== 0) {
            continue;
        }

        $global_setting_name = preg_replace('/^default_/', 'global_', $item['name'], 1);

        if (!isset($settings_names[$global_setting_name])) {
            continue;
        }

        $global_setting_id = $settings_names[$global_setting_name];
        $global_setting_item = $items[$global_setting_id];

        if ($global_setting_item['type'] !== $item['type']) {
            continue;
        }

        if (!$allow_global_individual_settings) {
            if ($item['name'] === 'default_product_details_view') {
                unset($items[$global_setting_id]);
                continue;
            }

            unset($items[$global_setting_id], $items[$key]);
            continue;
        }

        $item['global_setting'] = $global_setting_item;

        if (isset($global_setting_item['value'])) {
            $item['value'] = $global_setting_item['value'];
            $item['has_global_value'] = true;
        } else {
            $item['has_global_value'] = false;
        }

        unset($items[$global_setting_id]);
    }
    unset($item);

    $template->assign(array_merge($params, [
        'items' => $items
    ]));

    /** @var \Tygh\SmartyEngine\Core $smarty */
    $smarty = $template->getSmarty();

    return $smarty->fetch('common/settings_section.tpl', null, null, $template);
}
