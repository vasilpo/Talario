<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
