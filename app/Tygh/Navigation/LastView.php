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

namespace Tygh\Navigation;

class LastView
{
    /**
     * @var \Tygh\Navigation\LastView\ACommon
     */
    private static $instance;

    /**
     * Gets last view object instance
     *
     * @param string $area Area identifier
     *
     * @return \Tygh\Navigation\LastView\ACommon Object instance
     */
    public static function instance($area = AREA)
    {
        if (self::$instance === null) {
            /**
             * @psalm-var class-string<\Tygh\Navigation\LastView\ACommon> $class
             */
            $class = '\\Tygh\\Navigation\\LastView\\' . ucfirst(fn_get_area_name($area));
            self::$instance = new $class();
        }

        return self::$instance;
    }
}
