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

use Tygh\Addons\VendorPanelConfigurator\Enum\ColorSchemas;

defined('BOOTSTRAP') or die('Access denied');

return [
    ColorSchemas::LIGHT => [
        'element_color' => '#073763',
        'sidebar_color' => '#eef1f3',
    ],
    ColorSchemas::PURPLE => [
        'element_color' => '#674ea7',
        'sidebar_color' => '#f3edf7',
    ],
    ColorSchemas::LEMONGRASS => [
        'element_color' => '#38761d',
        'sidebar_color' => '#eaf5d6',
    ],
    ColorSchemas::SWEETY => [
        'element_color' => '#660000',
        'sidebar_color' => '#ffefee',
    ],
    ColorSchemas::SAND => [
        'element_color' => '#7f6000',
        'sidebar_color' => '#fdf1e0',
    ],
    ColorSchemas::CUSTOM  => [],
];
