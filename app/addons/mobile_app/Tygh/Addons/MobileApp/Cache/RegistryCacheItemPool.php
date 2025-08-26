<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\MobileApp\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
//phpcs:ignore
use Psr\Log\InvalidArgumentException;
use Tygh\Registry;

class RegistryCacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var string Cache key prefix
     */
    private $prefix = 'mobile_app_cache';

    /**
     * @var CacheItemInterface[] Pool of cache items
     */
    private $cache_pool = [];

    /**
     * @param string|null $key Cache key
     */
    private function getKey(string $key = null): string
    {
        return $this->prefix . ($key ? ('.' . $key) : '');
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return CacheItemInterface
     */
    public function getItem($key)
    {
        $item = new CacheItem($key);

        return $item->set(Registry::get($this->getKey($key)));
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = [])
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = Registry::get($this->getKey($key));
        }

        return $result;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key The key for which to check existence.
     *
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     */
    public function hasItem($key)
    {
        return Registry::isExist($this->getKey($key));
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     */
    public function clear()
    {
        return Registry::del($this->getKey());
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key The key to delete.
     *
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        return Registry::del($this->getKey($key));
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys An array of keys that should be removed from the pool.
     *
     * @throws InvalidArgumentException If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        Registry::registerCache($item->getKey(), ['ttl' => CACHE_ITEM_TTL]);

        return Registry::set($item->getKey(), $item->get());
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->cache_pool[] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     */
    public function commit()
    {
        foreach ($this->cache_pool as $cache_item) {
            $this->save($cache_item);
        }

        $this->cache_pool = [];

        return true;
    }
}
