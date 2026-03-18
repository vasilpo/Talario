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


namespace Tygh\Addons\CommerceML\Dto;

/**
 * Class ImageDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class ImageDto
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @param string $path        Image patch
     * @param string $description Image description
     *
     * @return \Tygh\Addons\CommerceML\Dto\ImageDto
     */
    public static function create($path, $description = '')
    {
        $object = new self();
        $object->path = (string) $path;
        $object->description = (string) $description;

        return $object;
    }
}
