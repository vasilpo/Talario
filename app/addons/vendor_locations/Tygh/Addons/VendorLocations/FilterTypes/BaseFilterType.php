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

namespace Tygh\Addons\VendorLocations\FilterTypes;

use Tygh\Addons\VendorLocations\Dto\Zone;
use Tygh\Addons\VendorLocations\Dto\Region;
use RuntimeException;
use Tygh\Tygh;

/**
 * Class BaseFilterType
 * Abstract class for geolocation filter types
 *
 * @package Tygh\Addons\VendorLocations\FilterTypes
 */
abstract class BaseFilterType
{
    /**
     * @return string
     */
    abstract public function buildSqlWhereConditions();

    /**
     * @return string
     */
    abstract public function buildSqlSelectExpression();
}
