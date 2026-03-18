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

namespace Tygh\Api;

/**
 * Data format interface
 */
interface IFormat
{
    /**
     * Must mime type(s) that can be encoded/decoded by this class
     *
     * @return array/string Mime type(s)
     */
    public function getMimeTypes();

    /**
     * Encodes $data in the format
     *
     * @param  array  $data resulting data that needs to be encoded in the given format
     * @return string Encoded string
     */
    public function encode($data);

    /**
    * Decodes $data from the format
    *
    * @param string $data data sent from client to the api in the given format
    * @return array Array of the parsed data
    */
    public function decode($data);
}
