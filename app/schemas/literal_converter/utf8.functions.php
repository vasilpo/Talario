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
 * Decode encoded string
 * Example: $result = fn_simple_decode_str('uftu'); // returns "test"
 *
 * @param string $str
 * @return string
 */
function fn_simple_decode_str($str)
{
    $decoded_str = '';
    for ($i = 0; $i < fn_strlen($str); $i++) {
        $chr = ord($str[$i]);
        $decoded_str .= chr(--$chr);
    }

    return $decoded_str;
}

/**
 * Encode plain text string
 * Example: $result = fn_simple_encode_str('test'); // returns "uftu"
 *
 * @param string $str
 * @return string
 */
function fn_simple_encode_str($str)
{
    $encoded_str = '';
    for ($i = 0; $i < fn_strlen($str); $i++) {
        $chr = ord($str[$i]);
        $encoded_str .= chr(++$chr);
    }

    return $encoded_str;
}
