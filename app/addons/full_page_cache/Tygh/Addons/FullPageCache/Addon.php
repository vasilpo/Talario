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

use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;

class Addon
{
    const GLOBAL_TAG_CACHE = 'ALL';

    /** @var \Tygh\Addons\FullPageCache\ProviderInterface */
    protected $provider;

    /** @var array<array<string|int>|string|int> */
    protected $schema = [];

    /** @var array<string> */
    protected $page_cache_tags = [];

    /** @var int */
    protected $page_cache_ttl = 0;

    /** @var bool */
    protected $is_allow_esi = false;

    /** @var bool */
    protected $is_cookie_send = false;

    /**
     * Addon constructor.
     *
     * @param array<string>                                $schema   Addon varnish schema
     * @param \Tygh\Addons\FullPageCache\ProviderInterface $provider Provider
     */
    public function __construct(array $schema, ProviderInterface $provider)
    {
        $this->schema = $schema;
        $this->provider = $provider;
    }

    /**
     * Checks whether server environment is suitable for full page caching.
     *
     * @return bool Whether the addon can be enabled.
     */
    public function canBeEnabled()
    {
        $result = true;

        if (ini_get('session.auto_start')) {
            $result = false;
            fn_set_notification(
                'E',
                __('full_page_cache.unable_to_enable_full_page_caching'),
                __('full_page_cache.error_session_auto_start_enabled')
            );
        }

        if (!$this->checkConnectionToCacheServer()) {
            $result = false;
            fn_set_notification(
                'E',
                __('full_page_cache.unable_to_enable_full_page_caching'),
                __('full_page_cache.unable_to_connect_to_varhish')
            );
        }

        $root_dir = Registry::get('config.dir.root');
        $symlink_path = $root_dir . '/esi.php';
        $target_path = 'app/addons/full_page_cache/esi.php';

        if (is_link($symlink_path) && readlink($symlink_path) === $target_path) {
            return $result;
        }

        if (!is_writable($root_dir)) {
            fn_set_notification(
                NotificationSeverity::ERROR,
                __('full_page_cache.unable_to_enable_full_page_caching'),
                __('full_page_cache.warning_root_dir_not_writable')
            );

            return false;
        }

        // Check if link is correct
        if (is_link($symlink_path)) {
            $current_target = readlink($symlink_path);
            // If link is incorrect - remove it
            if ($current_target !== $target_path || !file_exists($symlink_path)) {
                unlink($symlink_path);
            }
        } elseif (file_exists($symlink_path)) {
            // If it is not symlink, but just a file - remove it also
            unlink($symlink_path);
        }

        if (!file_exists($symlink_path)) {
            if (!symlink($target_path, $symlink_path)) {
                $result = false;
                fn_set_notification(
                    NotificationSeverity::ERROR,
                    __('full_page_cache.unable_to_enable_full_page_caching'),
                    __('full_page_cache.failed_to_create_symlink', ['[file]' => $symlink_path])
                );
            }
        }

        return $result;
    }

    /**
     * Checks connection to varnish server
     *
     * @return bool Whether connection to the varnish server is established and can be used.
     */
    public function checkConnectionToCacheServer()
    {
        return $this->provider->invalidateCacheByTags(['ping']);
    }

    /**
     * This method is being called after add-on has been enabled.
     * It regenerates cache-enabling VCL file and makes Varnish use it.
     *
     * @return void
     */
    public function onAddonEnable()
    {
        fn_set_notification(
            'N',
            __('successful'),
            __('full_page_cache.notice_caching_was_enabled'),
            'S'
        );
    }

    /**
     * Invalidates all cache records that are marked with any of the given tags.
     *
     * @param array<string> $tags List of cache tags
     *
     * @return bool
     */
    public function invalidateByTags(array $tags)
    {
        $tags = array_filter($tags, function ($tag) {
            return !$this->isIgnoredCacheTag($tag);
        });

        if (empty($tags)) {
            return false;
        }

        $tags = array_map(function ($tag) {
            return $this->mapStringToCacheTag($tag);
        }, $tags);

        return $this->provider->invalidateCacheByTags($tags);
    }

    /**
     * Registers given list of tags for the current page.
     *
     * @param array<string> $tags List of cache tags to register.
     *
     * @return void
     */
    public function registerPageCacheTags(array $tags)
    {
        foreach ($tags as $tag) {
            if (!$this->isIgnoredCacheTag($tag)) {
                $this->page_cache_tags[] = $this->mapStringToCacheTag($tag);
            }
        }
    }

    /**
     * Registers the current page cache time to live
     *
     * @param string $controller Controller
     * @param string $mode       Mode
     * @param string $action     Action
     *
     * @return void
     */
    public function registerPageCahceTTL($controller, $mode, $action)
    {
        $ttl = (int) $this->schema['cache_ttl'];

        foreach ($this->getDispatchesByPriority($controller, $mode, $action) as $dispatch) {
            if (isset($this->schema['cache_ttl_for_dispatches'][$dispatch])) {
                $ttl = (int) $this->schema['cache_ttl_for_dispatches'][$dispatch];
                break;
            }
        }

        $this->page_cache_ttl = $ttl;
    }

    /**
     * Sets the allowing use ESI
     *
     * @param bool $is_allow_esi Whether esi is allowed
     *
     * @return void
     */
    public function setIsAllowEsi($is_allow_esi)
    {
        $this->is_allow_esi = $is_allow_esi;
    }

    /**
     * Gets page headers
     *
     * @return string[]
     */
    public function getPageHeaders()
    {
        return $this->provider->buildPageHeaders(
            $this->page_cache_ttl,
            $this->page_cache_tags,
            $this->is_allow_esi
        );
    }

    /**
     * Wraps given block contents with ESI directives.
     *
     * @param array  $block         Block data
     * @param string $block_content Rendered HTML contents of the block
     * @param string $lang_code     Code of language in which block is rendered.
     * @param string $root_url      Root URL of the cart installation
     * @param string $requested_uri The URI of the requested page containing ESI directives.
     * @param bool   $debug         Whether to add debug HTML-comments above ESI directives.
     *
     * @return string ESI XML tags.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function renderESIForBlock(array $block, $block_content, $lang_code, $root_url, $requested_uri, $debug = false)
    {
        $block_render_url = sprintf(
            '%s/esi.php?block_id=%u&snapping_id=%u&lang_code=%s&requested_uri=%s',
            str_replace('https:', 'http:', rtrim($root_url, '\\/')),
            $block['block_id'],
            $block['snapping_id'],
            $lang_code,
            rawurlencode($requested_uri)
        );

        return $this->provider->renderESIBlock($block_render_url, $block_content, $debug);
    }

    /**
     * @param string $tag Cache tag
     *
     * @return bool
     */
    public function isIgnoredCacheTag($tag)
    {
        return in_array($tag, $this->schema['ignore_cache_tags'], true);
    }

    /**
     * Checks if the current dispatch is cacheable.
     *
     * @param string $controller Controller
     * @param string $mode       Mode
     * @param string $action     Action
     *
     * @return bool
     */
    public function isDispatchCacheable($controller, $mode, $action)
    {
        $dispatches = $this->getDispatchesByPriority($controller, $mode, $action);

        foreach ($dispatches as $dispatch) {
            if (in_array($dispatch, $this->schema['disable_for_dispatches'], true)) {
                return false;
            }
        }

        foreach ($dispatches as $dispatch) {
            if (in_array($dispatch, $this->schema['dispatches'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool Whether the current request is an ESI request.
     */
    public function isEsiRequest()
    {
        return $this->provider->isEsiRequest();
    }

    /**
     * @return array<string>
     */
    public function getPageCacheTags()
    {
        return $this->page_cache_tags;
    }

    /**
     * @return int
     */
    public function getPageCacheTtl()
    {
        return $this->page_cache_ttl;
    }

    /**
     * @return bool
     */
    public function isIsAllowEsi()
    {
        return $this->is_allow_esi;
    }

    /**
     * @param string   $name        Cookie name
     * @param string   $value       Cookie value
     * @param int|null $expire_time Expire time
     *
     * @return void
     */
    public function setCookie($name, $value, $expire_time = null)
    {
        if ($expire_time === null) {
            $expire_time = strtotime('+64 days');
        }

        setcookie($this->getCookieName($name), $value, $expire_time, '/');
        $this->is_cookie_send = true;
    }

    /**
     * @param string $name Cookie name
     *
     * @return bool
     */
    public function hasCookie($name)
    {
        return isset($_COOKIE[$this->getCookieName($name)]);
    }

    /**
     * @param string      $name          Cookie name
     * @param string|null $default_value Default value to return
     *
     * @return string|null
     */
    public function getCookie($name, $default_value = null)
    {
        return $this->hasCookie($name) ? $_COOKIE[$this->getCookieName($name)] : $default_value;
    }

    /**
     * @param string $name Cookie name
     *
     * @return void
     */
    public function removeCookie($name)
    {
        setcookie($this->getCookieName($name), '', time(), '/');
        $this->is_cookie_send = true;
    }

    /**
     * @return bool
     */
    public function isCookieSend()
    {
        return $this->is_cookie_send;
    }

    /**
     * Gets cookie name
     *
     * @param string $name Cookie name
     *
     * @return string
     */
    protected function getCookieName($name)
    {
        return sprintf('fpc_%s', $name);
    }

    /**
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getGlobalCacheTags()
    {
        return $this->schema['global_cache_tags'];
    }

    /**
     * Creates a short hash of given value that can be used as a cache tag.
     *
     * @param string $string Value to map.
     *
     * @return string Short hash of given value.
     */
    protected function mapStringToCacheTag($string)
    {
        return substr(sha1($string), 0, 7);
    }

    /**
     * Gets dispatches list by priority.
     *
     * @param string $controller Controller
     * @param string $mode       Mode
     * @param string $action     Action
     *
     * @return array<string>
     */
    protected function getDispatchesByPriority($controller, $mode, $action)
    {
        return [
            implode('.', [$controller, $mode, $action]),
            implode('.', [$controller, $mode, '*']),
            implode('.', [$controller, $mode]),
            implode('.', [$controller, '*']),
        ];
    }
}
