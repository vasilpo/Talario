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
 * Text format encoder/decoder
 */
class Text implements IFormat
{
    protected $mime_types = array(
        'text/plain'
    );

    public function getMimeTypes()
    {
        return $this->mime_types;
    }

    public function encode($data)
    {
        return $data;
    }

    public function decode($data)
    {
        parse_str($data, $result);

        return array($result, '');
    }
}
