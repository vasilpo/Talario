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

namespace Tygh\Addons\FullPageCache;

/**
 * Interface ProviderInterface
 *
 * @package Tygh\Addons\FullPageCache
 */
interface ProviderInterface
{
    /**
     * Gets HTTP header that contains cache dependecies for the current page.
     *
     * @param int           $ttl          Cache time to live on seconds.
     * @param array<string> $tags         List of cache tags
     * @param bool          $is_allow_esi Whether the use ESI
     *
     * @return string[]
     */
    public function buildPageHeaders($ttl = 180, array $tags = [], $is_allow_esi = false);

    /**
     * Invalidates all cache records that are marked with any of the given tags.
     *
     * @param array<string> $tags List of cache tags
     *
     * @return bool
     */
    public function invalidateCacheByTags(array $tags);

    /**
     * @return bool Whether the current request is an ESI request.
     */
    public function isEsiRequest();

    /**
     * Wraps given block contents with ESI directives.
     *
     * @param string $url           Block render URL
     * @param string $block_content Block content
     * @param bool   $debug         Enable debug
     *
     * @return string ESI XML tags.
     */
    public function renderESIBlock($url, $block_content, $debug = false);
}
