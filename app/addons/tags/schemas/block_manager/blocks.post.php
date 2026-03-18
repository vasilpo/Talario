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

$schema['tags'] = array (
    'content' => array (
        'items' => array (
            'remove_indent' => true,
            'hide_label' => true,
            'type' => 'enum',
            'object' => 'tags',
            'items_function' => 'fn_get_tags',
            'fillings' => array (
                'tag_cloud' => array (
                    'params' => array (
                        'status' => 'A',
                        'sort_by' => 'popularity',
                        'sort_order' => 'desc',
                        'sort_popular' => true,
                        'only_active_objects' => true,
                    ),
                    'settings' => array(
                        'limit' => array (
                            'type' => 'input',
                            'default_value' => 50
                        )
                    )
                )
            ),
        ),
    ),
    'templates' => array (
        'addons/tags/blocks/tag_cloud.tpl' => array (
            'fillings' => array ('tag_cloud')
        )
    ),
    'wrappers' => 'blocks/wrappers',
    'cache' => array (
        'update_handlers' => array('tags', 'tag_links', 'products', 'pages'),
    ),
    'brief_info_function' => 'fn_block_get_block_with_items_info'
);

$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'tags';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'tag_links';

return $schema;
