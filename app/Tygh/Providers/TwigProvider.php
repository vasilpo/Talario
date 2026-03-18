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
use Tygh\Registry;
use Tygh\Twig\TwigCacheFilesystem;
use Tygh\Twig\TwigCoreExtension;
use Tygh\Twig\TwigEnvironment;
use Twig\Loader\ArrayLoader;

/**
 * The provider class that registers the twig component in the Tygh::$app container.
 * 
 * @package Tygh\Providers
 */
class TwigProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['twig'] = function ($app) {
            $loader = new ArrayLoader([]);
            $twig = new TwigEnvironment($loader, array(
                'cache' => new TwigCacheFilesystem(Registry::get('config.dir.cache_twig_templates')),
                'auto_reload' => true,
                'autoescape' => false,
                'debug' => fn_is_development()
            ));

            $twig->addExtension(new TwigCoreExtension());

            return $twig;
        };
    }
}