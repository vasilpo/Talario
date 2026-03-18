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
use Symfony\Component\Lock\Store\RedisStore as BaseRedisStore;
use Tygh\Lock\StoreInterface;

/**
 * RedisStore
 *
 * @package Tygh\Lock\Store
 */
class RedisStore extends BaseRedisStore implements StoreInterface
{
    /**
     * @var \Predis\Client|\Redis|\RedisArray|\RedisCluster|\Tygh\Lock\Store\RedisProxy
     */
    protected $redis;

    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redis_client
     * @param float                                           $initial_ttl the expiration delay of locks in seconds
     */
    public function __construct($redis_client, $initial_ttl = 300.0)
    {
        parent::__construct($redis_client, $initial_ttl);

        $this->redis = $redis_client;
    }

    /**
     * @inheritDoc
     */
    public function exists(Key $key, $owned_to_current_process = true)
    {
        if ($owned_to_current_process) {
            return parent::exists($key);
        } else {
            return $this->redis->get((string) $key) !== false;
        }
    }
}