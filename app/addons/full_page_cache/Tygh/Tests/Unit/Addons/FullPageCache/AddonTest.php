<?php
namespace Tygh\Tests\Unit\Addons\FullPageCache;

use Tygh\Addons\FullPageCache\Addon;
use Tygh\Tests\Unit\ATestCase;
use Tygh\Addons\FullPageCache\ProviderInterface;

class AddonTest extends ATestCase
{
    /**
     * @param string $controller Controller
     * @param string $mode       Mode
     * @param string $action     Action
     * @param bool   $expected   Expected value
     * @dataProvider dpIsDispatchCacheable
     *
     * @return void
     */
    public function testIsDispatchCacheable($controller, $mode, $action, $expected)
    {
        $addon = new Addon($this->getSchema(), new FakeProvider());

        $this->assertEquals($expected, $addon->isDispatchCacheable($controller, $mode, $action));
    }

    /**
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function dpIsDispatchCacheable()
    {
        return [
            ['index', 'index', '', true],
            ['products', 'detail', '', true],
            ['products', 'detail', 'action', true],
            ['options', 'list', '', false],
            ['options', 'list', 'action', false],
            ['products', 'search', '', false],
            ['products', 'search', 'action', false],
        ];
    }


    /**
     * @param string $controller Controller
     * @param string $mode       Mode
     * @param string $action     Action
     * @param int    $expected   Expected value
     * @dataProvider dpRegisterPageCahceTTL
     *
     * @return void
     */
    public function testRegisterPageCahceTTL($controller, $mode, $action, $expected)
    {
        $addon = new Addon($this->getSchema(), new FakeProvider());

        $addon->registerPageCahceTTL($controller, $mode, $action);
        $this->assertEquals($expected, $addon->getPageCacheTtl());
    }

    /**
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function dpRegisterPageCahceTTL()
    {
        return [
            ['index', 'index', '', 90],
            ['products', 'detail', '', 240],
            ['products', 'detail', 'action', 20],
            ['categories', 'detail', '', 45],
            ['categories', 'detail', 'action', 45],
            ['products', 'list', '', 180],
            ['products', 'list', 'action', 180],
            ['tags', 'list', '', 90],
            ['tags', 'list', 'action', 90],
        ];
    }

    /**
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    protected function getSchema()
    {
        return [
            // TTL for objects at cache, seconds
            'cache_ttl' => 90,

            // Which dispatches should be cached
            'dispatches' => [
                'index.index',
                'products.*',
                'categories.*',
                'pages.*',
                'product_features.view',
                'promotions.*',
                'robots.*',
                'sitemap.*',
                'xml_feeds.*',
                'phone_masks.*',
                'discussion.*',
                'tags.*',
            ],

            // Which dispatches must not be cached
            'disable_for_dispatches' => [
                'products.search'
            ],

            // Which tables  must be ignored on changes
            'ignore_tables' => [
                'cache_handlers',
                'lock_keys'
            ],

            // TTL for objects at cache by dispabtches, seconds
            'cache_ttl_for_dispatches' => [
                'products.*' => 180,
                'products.detail' => 240,
                'products.detail.action' => 20,
                'categories.detail' => 45
            ]
        ];
    }
}

class FakeProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public function buildPageHeaders($ttl = 180, array $tags = [], $is_allow_esi = false)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function invalidateCacheByTags(array $tags)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isEsiRequest()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function renderESIBlock($url, $block_content, $debug = false)
    {
        return '';
    }
}