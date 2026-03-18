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

namespace Tygh\Location;

/**
 * Interface IUserDataStorage describes an interface of the user data storage object for the customer location manager.
 *
 * @see \Tygh\Location\Manager
 *
 * @package Tygh\Location
 */
interface IUserDataStorage
{
    /**
     * Gets storage item value.
     *
     * @param string|int $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Gets all values from storage.
     *
     * @return array
     */
    public function getAll();

    /**
     * Sets storage item value.
     *
     * @param string|int $key
     * @param mixed      $value
     */
    public function set($key, $value);

    /**
     * Deletes storage item.
     *
     * @param string|int $key
     */
    public function delete($key);
}
