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

namespace Tygh\Api\Formats;

/**
 * Class FormData provides parsing for API requests with multipart/form-data content type.
 *
 * @package Tygh\Api\Formats
 */
class Form extends Text
{
    protected $mime_types = [
        'multipart/form-data',
        'application/x-www-form-urlencoded',
    ];

    public function decode($data)
    {
        $decoded_data = [];
        if (!empty($_POST)) {
            $decoded_data = $_POST;
        }

        return [$decoded_data, ''];
    }
}
