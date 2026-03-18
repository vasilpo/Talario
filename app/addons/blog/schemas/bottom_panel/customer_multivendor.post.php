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
use Tygh\Tools\Url;

include_once(Registry::get('config.dir.schemas') . 'bottom_panel/vendor.functions.php');

$schema['pages.view']['to_vendor'] = function (Url $url) {
    $page_id = $url->getQueryParam('page_id');

    if (empty($page_id)) {
        return false;
    }

    if ($page_id == fn_blog_get_first_blog_page_id()) {
        return [
            'dispatch' => 'pages.manage',
            'page_type' => PAGE_TYPE_BLOG
        ];
    } else {
        return fn_bottom_panel_mve_get_page_url_params($url);
    }
};

return $schema;