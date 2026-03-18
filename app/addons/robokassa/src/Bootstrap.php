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


namespace Tygh\Addons\Robokassa;


use Tygh\Core\ApplicationInterface;
use Tygh\Core\BootstrapInterface;
use Tygh\Core\HookHandlerProviderInterface;

/**
 * This class describes instructions for loading the robokassa add-on
 *
 * @package Tygh\Addons\Robokassa
 */
class Bootstrap implements BootstrapInterface, HookHandlerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function boot(ApplicationInterface $app)
    {
        $app->register(new ServiceProvider());
    }

    /**
     * @inheritDoc
     */
    public function getHookHandlerMap()
    {
        if (!fn_allowed_for('MULTIVENDOR')) {
            return [];
        }

        return [
            'get_companies' => [
                'addons.robokassa.hook_handlers.companies',
                'onGetCompanies',
            ],
            'get_payments'  => [
                'addons.robokassa.hook_handlers.payments',
                'onGetPayments',
            ],
            'get_payment_processors_post' => [
                'addons.robokassa.hook_handlers.payments',
                'onGetPaymentProcessorsPost',
            ],
            'change_order_status' => [
                'addons.robokassa.hook_handlers.orders',
                'onChangeOrderStatus',
            ],
        ];
    }
}
