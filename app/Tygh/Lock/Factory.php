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

/**
 * @package Tygh\Lock
 */
class Factory
{
    /**
     * @var \Tygh\Lock\StoreInterface
     */
    protected $store;

    /**
     * Factory constructor.
     *
     * @param \Tygh\Lock\StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @inheritdoc
     */
    public function createLock($resource, $ttl = 30.0, $auto_release = true)
    {
        return new Lock(new Key($resource), $this->store, $ttl, $auto_release);
    }
}