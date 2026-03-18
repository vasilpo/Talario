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

use Tygh\Enum\BackofficeColorSchemeVariants;

defined('BOOTSTRAP') or die('Access denied');

return [
    BackofficeColorSchemeVariants::DARK => [
        'type' => BackofficeColorSchemeVariants::DARK,
        'description' => __('backoffice_color_scheme.dark_mode'),
    ],
    BackofficeColorSchemeVariants::LIGHT => [
        'type' => BackofficeColorSchemeVariants::LIGHT,
        'description' => __('backoffice_color_scheme.light_mode'),
    ],
    BackofficeColorSchemeVariants::SYSTEM => [
        'type' => BackofficeColorSchemeVariants::SYSTEM,
        'description' => __('backoffice_color_scheme.system_mode'),
    ],
];
