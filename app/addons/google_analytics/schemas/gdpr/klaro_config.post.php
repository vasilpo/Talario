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

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['services']['google-analytics'] = [
    'purposes'     => ['performance'],
    'name'         => 'google-analytics',
    'translations' => [
        'zz' => [
            'title'       => 'google_analytics.google_analytics_cookies_title',
            'description' => 'google_analytics.google_analytics_cookies_description'
        ],
    ],
];

$schema['services']['google-ads'] = [
    'purposes'     => ['marketing'],
    'name'         => 'google-ads',
    'translations' => [
        'zz' => [
            'title'       => 'google_analytics.google_ads_cookies_title',
            'description' => 'google_analytics.google_ads_cookies_description'
        ],
    ],
];

return $schema;
