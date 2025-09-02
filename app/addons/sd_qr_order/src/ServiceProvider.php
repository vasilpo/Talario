<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\SdQrOrder;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\SdQrOrder\HookHandlers\CartHookHandler;
use Tygh\Application;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.sd_qr_order.hook_handlers.cart'] = static function () {
            return new CartHookHandler();
        };

    }
}
