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

use Tygh\Api\IFormat;

/**
 * JSON format encoder/decoder
 */
class Json implements IFormat
{
    protected $mime_types = array(
        'application/json',
        'application/javascript'
    );

    public function getMimeTypes()
    {
        return $this->mime_types;
    }

    public function encode($data)
    {
        return json_encode($data);
    }

    public function decode($data)
    {
        $result = json_decode($data, true);
        $error = json_last_error() !== JSON_ERROR_NONE ? json_last_error_msg() : '';

        return array($result, $error);
    }
}
