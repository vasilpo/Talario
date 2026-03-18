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

$schema['pages']['check_params'] = function($request) use ($schema) {

    $dispatch = $schema['pages']['customer_dispatch'];
    $page_type = '';
    if (!empty($request['page_id'])) {
        $page_type = db_get_field("SELECT page_type FROM ?:pages WHERE page_id = ?i", $request['page_id']);
    } elseif (!empty($request['page_type'])) {
        $page_type = $request['page_type'];
    }
    $suffix = ($page_type == PAGE_TYPE_BLOG) ? '?page_type=' . PAGE_TYPE_BLOG : '';

    return $dispatch . $suffix;
};

return $schema;
