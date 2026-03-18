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

/**
 * Describes a way to describe grids
 *
 * Structure:
 *
 * 'wrappers' => [
 *      'wrapper_name' => [
 *          'name' => __('wrapper_name_langvar'),
 *          'template' => 'template_name.tpl',
 *          'allowed_locations' => [
 *              'location1',
 *              'location2',
 *              ... // list of location dispatches (ex. 'checkout' for Checkout location)
 *          ]
 *      ]
 *  ]
 */

/** @var array<string, array> $schema */
$schema = [
    'wrappers' => [
        'lite_checkout' => [
            'name' => __('block_manager.wrappers.lite_checkout'),
            'template' => 'blocks/grid_wrappers/lite_checkout.tpl',
            'allowed_locations' => [
                'checkout'
            ],
        ]
    ],
];

return $schema;
