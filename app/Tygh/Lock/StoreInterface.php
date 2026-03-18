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

namespace Tygh\Lock;

use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\PersistingStoreInterface as BaseStoreInterface;

/**
 * Extends StoreInterface
 *
 * @package Tygh\Lock
 */
interface StoreInterface extends BaseStoreInterface
{
    /**
     * Returns whether or not the resource exists in the storage.
     *
     * @param \Symfony\Component\Lock\Key $key                      Key
     * @param bool                        $owned_to_current_process Whether to check if key owned to current process
     *
     * @return bool
     */
    public function exists(Key $key, $owned_to_current_process = true);
}