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
 * BlockManagerActions contains actions that can be performed with the snapping element in the block manager.
 *
 * @package Tygh\Enum
 */
class BlockManagerActions
{
    const ACT_PROPERTIES = 'properties';
    const ACT_SWITCH = 'switch';
    const ACT_DELETE = 'delete';

    public static function getAll()
    {
        return array(
            self::ACT_PROPERTIES => self::ACT_PROPERTIES,
            self::ACT_SWITCH => self::ACT_SWITCH,
            self::ACT_DELETE => self::ACT_DELETE,
        );
    }
}