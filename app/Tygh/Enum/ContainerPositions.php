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

namespace Tygh\Enum;

/**
 * Class ContainerPositions
 * @package Tygh\Enum
 */
class ContainerPositions
{
    const TOP_PANEL = 'TOP_PANEL';
    const HEADER = 'HEADER';
    const CONTENT = 'CONTENT';
    const FOOTER = 'FOOTER';

    public static function getAll()
    {
        return array(
            self::TOP_PANEL => self::TOP_PANEL,
            self::HEADER => self::HEADER,
            self::CONTENT => self::CONTENT,
            self::FOOTER => self::FOOTER,
        );
    }
}