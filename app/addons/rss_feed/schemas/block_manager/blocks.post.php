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

require_once Registry::get('config.dir.addons') . 'rss_feed/schemas/block_manager/blocks.functions.php';

$schema['rss_feed'] = array (
    'content' => array (
        'filling' => array(
            'type' => 'selectbox',
            'values' => array (
                'products' => 'products',
            ),
            'default_value' => 'products',
            'values_settings' => array(
                'products' => array(
                    'settings' => array(
                        'rss_sort_by' => array (
                            'type' => 'selectbox',
                            'values' => array (
                                'A' => 'rss_created',
                                'U' => 'rss_updated'
                            )
                        ),
                        'rss_display_sku' => array (
                            'type' => 'checkbox',
                        ),
                        'rss_display_image' => array (
                            'type' => 'checkbox',
                        ),
                        'rss_display_price' => array (
                            'type' => 'checkbox',
                        ),
                        'rss_display_original_price' => array (
                            'type' => 'checkbox',
                        ),
                        'rss_display_add_to_cart' => array (
                            'type' => 'checkbox',
                        ),
                    )
                )
            )
        )
    ),
    'templates' => array (
        'addons/rss_feed/blocks/rss_feed.tpl' => array(),
    ),
    'wrappers' => 'blocks/wrappers',
    'settings' => array (
        'max_item' => array (
            'type' => 'input',
            'default_value' => Registry::get('settings.Appearance.elements_per_page')
        ),
        'feed_title' => array (
            'type' => 'input',
            'default_value' => ''
        ),
        'feed_description' => array (
            'type' => 'input',
            'default_value' => ''
        ),
    ),
    'cache' => true,
    'brief_info_function' => 'fn_block_get_rss_object_info'
);

return $schema;
