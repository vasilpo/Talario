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


namespace Tygh\Addons\CommerceML\Tools;

use Tygh\Registry;

/**
 * Class RuntimeCacheStorage
 *
 * @package Tygh\Addons\CommerceML\Tools
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 */
class RuntimeCacheStorage
{
    /**
     * @var array<string|int, mixed>
     */
    private $cache = [];

    /**
     * @var array<string|int, int>
     */
    private $cache_usage_counts = [];

    /**
     * @var int Storage items limit
     */
    private $limit;

    /**
     * RuntimeCacheStorage constructor.
     *
     * @param int $limit Limit of storage items.
     */
    public function __construct($limit = 0)
    {
        $limit = $limit ?: Registry::ifGet('config.tweaks.commerceml.cache_storage.limit', 10);
        $this->limit = (int) $limit;
    }

    /**
     * Gets item from cache by key
     *
     * @param string|int $key           Cache key
     * @param mixed|null $default_value Default value if cache not found
     *
     * @return mixed|null
     */
    public function get($key, $default_value = null)
    {
        if (!$this->has($key)) {
            return $default_value;
        }

        $this->cache_usage_counts[$key]++;

        return $this->cache[$key];
    }

    /**
     * Check if storage has cache key
     *
     * @param string|int $key Cache key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * Gets or set cache
     *
     * @param string|int $key           Cache key
     * @param callable   $default_value Callable function which will be executed if storage has not cache item.
     *
     * @return mixed|null
     */
    public function getOrSet($key, callable $default_value)
    {
        if (!$this->has($key)) {
            $this->add($key, $default_value());
        }

        return $this->get($key);
    }

    /**
     * Adds cache item to storage
     *
     * @param string|int $key   Cache key
     * @param mixed      $value Cache value
     */
    public function add($key, $value)
    {
        if (!isset($this->cache[$key])) {
            $this->checkLimitForAdd();
        }

        $this->cache[$key] = $value;

        if (isset($this->cache_usage_counts[$key])) {
            return;
        }

        $this->cache_usage_counts[$key] = 0;
    }

    /**
     * Removes cache item from storage
     *
     * @param string|int $key Cache key
     */
    public function remove($key)
    {
        unset($this->cache[$key], $this->cache_usage_counts[$key]);
    }

    /**
     * Gets items count
     *
     * @return int
     */
    public function count()
    {
        return count($this->cache);
    }

    /**
     * Clears storage
     */
    public function clear()
    {
        $this->cache = [];
        $this->cache_usage_counts = [];
    }

    /**
     * Checks storage items limit for add new item
     */
    private function checkLimitForAdd()
    {
        if ($this->count() <= $this->limit - 1) {
            return;
        }

        asort($this->cache_usage_counts);
        reset($this->cache_usage_counts);
        $key = key($this->cache_usage_counts);

        $this->remove($key);
    }
}
