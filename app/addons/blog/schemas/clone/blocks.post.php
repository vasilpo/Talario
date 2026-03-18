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

include_once(__DIR__ . '/blocks.functions.php');

$schema['blog'] = array(
    'function' => 'fn_ult_clone_layout_block_configured_by_filling',
    'config' => array(
        'fillings_handlers' => array(
            'blog.recent_posts_scroller' => array('fn_ult_clone_layout_block_pages_filling_by_tree'),
            'blog.text_links' => array('fn_ult_clone_layout_block_pages_filling_by_tree')
        )
    )
);

if (!empty($schema['rss_feed'])) {
    $schema['rss_feed']['config']['properties_handlers']['filling'][] = 'fn_ult_clone_layout_block_rss_feed_filling_by_blog';
}

return $schema;