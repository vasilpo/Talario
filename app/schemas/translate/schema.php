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

return array (
    'addons' => array (
        'manage' => array (
            'installed_addon' => array (
                'dimension' => 0,
                'table_name' => 'addon_descriptions',
                'fields' => array ('name' => 'name'),
                'where_fields' => array(
                    'addon' => 'addon',
                )
            )
        ),
        'update' => array (
            'options' => array (
                'dimension' => 2,
                'unescape' => true,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'description'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'subsections' => array (
                'dimension' => 1,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'description'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'sections' => array (
                'dimension' => 1,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'title'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'variants' => array (
                'dimension' => 1,
                'unescape' => true,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'value'),
                'where_fields' => array(
                    'object_id' => 'variant_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            )
        )
    ),
    'settings' => array (
        'manage' => array (
            'options' => array (
                'dimension' => 2,
                'unescape' => true,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'description'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'subsections' => array (
                'dimension' => 1,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'description'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'sections' => array (
                'dimension' => 1,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'title'),
                'where_fields' => array(
                    'object_id' => 'object_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            ),
            'variants' => array (
                'dimension' => 1,
                'unescape' => true,
                'table_name' => 'settings_descriptions',
                'fields' => array ('value' => 'value'),
                'where_fields' => array(
                    'object_id' => 'variant_id',
                    'object_type' => array('object_type' => 'object_type'),
                ),
            )
        )
    ),
    'any' => array (
        'any' => array (
            'blocks' => array (
                'dimension' => 1,
                'table_name' => 'block_descriptions',
                'fields' => array ('block'),
                'where_fields' => array(
                    'block_id' => 'block_id'
                )
            )
        )
    ),
    'categories' => array (
        'manage' => array (
            'categories_tree' => array (
                'dimension' => 1,
                'table_name' => 'category_descriptions',
                'fields' => array ('category'),
                'where_fields' => array(
                    'category_id' => 'category_id'
                ),
                'inner' => array('subcategories', 1)
            )
        ),
        'update' => array (
            'category_data' => array (
                'dimension' => 0,
                'table_name' => 'category_descriptions',
                'fields' => array ('category', 'description', 'page_title', 'meta_keywords', 'meta_description'),
                'where_fields' => array(
                    'category_id' => 'category_id'
                )
            )
        )
    ),
    'products' => array (
        'manage' => array (
            'products' => array (
                'dimension' => 1,
                'table_name' => 'product_descriptions',
                'fields' => array ('product', 'short_description', 'full_description', 'shortname', 'meta_keywords', 'meta_description', 'search_words', 'page_title'),
                'where_fields' => array(
                    'product_id' => 'product_id'
                ),
            )
        ),
        'update' => array (
            'product_data' => array (
                'dimension' => 0,
                'table_name' => 'product_descriptions',
                'fields' => array ('product', 'short_description', 'full_description', 'shortname', 'meta_keywords', 'meta_description', 'search_words', 'page_title'),
                'where_fields' => array(
                    'product_id' => 'product_id'
                )
            )
        )
    )
);
