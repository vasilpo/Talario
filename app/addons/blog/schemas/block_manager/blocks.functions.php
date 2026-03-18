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

function fn_block_get_blog_info(array $block, $lang_code = CART_LANGUAGE)
{
    $items = isset($block['content']['items']) ? $block['content']['items'] : [];
    $filling = isset($items['filling']) ? (string) $items['filling'] : '' ;
    $limit = isset($items['limit']) ? $items['limit'] : (isset($block['properties']['limit']) ? $block['properties']['limit'] : 0);
    $filling_text = fn_is_lang_var_exists($filling) ? __($filling, [], $lang_code) : '';
    $content = ($filling_text) ? sprintf('%s, %s', $filling_text, __('n_posts', [$limit], $lang_code)) : __('n_posts', [$limit], $lang_code);

    return [
        'content' => $content,
    ];
}
