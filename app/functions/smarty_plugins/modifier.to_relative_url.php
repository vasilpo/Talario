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

defined('BOOTSTRAP') or die('Access denied');

/**
 * Smarty plugin
 *
 * Type:    modifier
 * Name:    to_relative_url
 * Purpose: Converts an absolute url to a relative
 *
 * @param string $url Absolute url
 *
 * @return string
 */
function smarty_modifier_to_relative_url($url)
{
    $relative_url = '';

    if (!$url) {
        return $relative_url;
    }

    $url = ltrim($url, '\\');
    $parsed_url = parse_url($url);

    if (
        !empty($parsed_url['path'])
        || !empty($parsed_url['query'])
    ) {
        $relative_url = sprintf('%s?%s', ltrim($parsed_url['path'] ?? '', '/'), $parsed_url['query'] ?? '');
    }

    return $relative_url;
}
