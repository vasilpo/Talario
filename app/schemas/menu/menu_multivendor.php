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
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Settings;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['central']['vendors'] = [
    'title' => __('vendors_menu_title'),
    'items' => [
        'vendor_accounting' => [
            'href' => 'companies.balance',
            'position' => 200,
        ],
        'vendors' => [ // (!) Don't move it. The "Vendors" menu must be the last one for the active menu item.
            'href' => 'companies.manage',
            'position' => 100,
        ],
    ],
    'position' => 350,
    'icon' => 'group',
];

$schema['top']['settings']['items']['Vendors'] = [
    'href' => 'settings.manage?section_id=Vendors',
    'position' => 950,
    'type' => 'setting',
];

$schema['top']['administration']['items']['Storefronts'] = [
    'href'     => 'storefronts.manage',
    'position' => 1350,
    'icon' => 'shopping_cart',
];

if (!isset($_REQUEST['s_storefront']) && isset($_REQUEST['storefront_id'])) {
    $schema['top']['administration']['items']['languages']['subitems']['edit_on_site']['href'] =
        'customization.update_mode?type=live_editor&status=enable&s_storefront=' . $_REQUEST['storefront_id'];
}

if (
    fn_allowed_for('MULTIVENDOR:ULTIMATE')
    && !empty(Tygh::$app['session']['auth']['storefront_id'])
) {
    $sections = $schema['top']['settings']['items'];
    $storefront_id = (int) Tygh::$app['session']['auth']['storefront_id'];
    $sections = fn_filter_settings_sections_by_accessibility(
        $sections,
        Settings::instance(['storefront_id' => $storefront_id])->getCoreSections()
    );

    $schema['top']['settings']['items'] = [];
    foreach ($sections as $name => $value) {
        $schema['top']['settings']['items'][$name] = [
            'href'     => isset($value['href']) ? $value['href'] : '',
            'position' => $value['position'],
            'type'     => $value['type'],
        ];
    }

    unset(
        $schema['top']['administration']['items']['profile_fields'],
        $schema['top']['administration']['items']['languages']['subitems']['edit_on_site'],
        $schema['top']['administration']['items']['languages']['subitems']['edit_on_site_divider'],
        $schema['top']['administration']['items']['languages']['subitems']['translations'],
        $schema['top']['administration']['items']['statuses_management']
    );
}

return $schema;
