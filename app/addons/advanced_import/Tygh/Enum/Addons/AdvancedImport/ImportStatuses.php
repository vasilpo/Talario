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

namespace Tygh\Enum\Addons\AdvancedImport;

class ImportStatuses
{
    const NOOP = 'X';
    const IN_PROGRESS = 'P';
    const SUCCESS = 'S';
    const FAIL = 'F';

    public static function getAll()
    {
        return array(static::NOOP, static::IN_PROGRESS, static::SUCCESS, static::FAIL);
    }

    public static function getFinished()
    {
        return array(static::FAIL, static::SUCCESS);
    }
}