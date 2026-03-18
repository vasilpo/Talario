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


namespace Tygh\Lock\Store;


use Symfony\Component\Lock\Key;
use Tygh\Lock\StoreInterface;

/**
 * DummyStore is a dummy store.
 * Usable to disable locks mechanism.
 *
 * @package Tygh\Backend\Lock
 */
class DummyStore implements StoreInterface
{
    /**
     * @inheritDoc
     */
    public function save(Key $key)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function putOffExpiration(Key $key, $ttl)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(Key $key)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function exists(Key $key, $owned_to_current_process = true)
    {
        return false;
    }
}