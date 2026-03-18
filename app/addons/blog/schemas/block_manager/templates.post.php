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

$schema['addons/blog/blocks/recent_posts_scroller.tpl'] = array (
    'fillings' => array('blog.recent_posts_scroller'),
    'params' => array (
        'plain' => true,
        'request' => array (
            'blog_page_id' => '%PAGE_ID%',
        ),
    ),
    'settings' => array (
        'limit' => array (
            'type' => 'input',
            'default_value' => 3
        ),
        'not_scroll_automatically' => array (
            'type' => 'checkbox',
            'default_value' => 'Y'
        ),
        'speed' =>  array (
            'type' => 'input',
            'default_value' => 400,
            'tooltip'       => __('tooltip_carousel_speed')
        ),
        'pause_delay' =>  array (
            'type' => 'input',
            'default_value' => 3
        ),
        'item_quantity' =>  array (
            'type' => 'input',
            'default_value' => 3
        ),
        'outside_navigation' => array (
            'type' => 'checkbox',
            'default_value' => 'Y'
        ),
    ),
);

$schema['addons/blog/blocks/recent_posts.tpl'] = array (
    'fillings' => array('blog.recent_posts'),
    'params' => array (
        'plain' => true,
        'request' => array (
            'blog_page_id' => '%PAGE_ID%',
        ),
    )
);

$schema['addons/blog/blocks/text_links.tpl'] = array (
    'fillings' => array('blog.text_links'),
    'params' => array (
        'plain' => true
    )
);

return $schema;
