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

namespace Tygh\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\FullPageCache\Addon;
use Tygh\Addons\FullPageCache\Provider\VarnishProvider;
use Tygh\Application;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class FullPageCacheProvider registers components used by "Full-page cache" add-on at Application container.
 *
 * @package Tygh\Providers
 */
final class FullPageCacheProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     *
     * @return void
     */
    public function register(Container $pimple)
    {
        Tygh::$app['addons.full_page_cache'] = static function (Application $app) {
            return new Addon(
                fn_get_schema('full_page_cache', 'varnish'),
                Tygh::$app['addons.full_page_cache.provider']
            );
        };

        Tygh::$app['addons.full_page_cache.provider'] = static function (Application $app) {
            $params = fn_explode(':', Registry::get('addons.full_page_cache.varnish_host'));
            $varnish_host = reset($params);
            $varnish_port = isset($params[1]) ? (int) $params[1] : 80;

            return new VarnishProvider($varnish_host, $varnish_port);
        };
    }
}
