<?php
/***************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\SdDesignChanges;

use Tygh\Addons\SdDesignChanges\HookHandlers\ProductsHookHandler;
use Tygh\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider is intended to register services and components of the sd_design_changes add-on to the
 * application container.
 *
 * @package Tygh\Addons\SdDesignChanges
 *
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $app Application instance
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.sd_design_changes.hook_handlers.products'] = static function (Application $app) {
            return new ProductsHookHandler();
        };
    }
}
