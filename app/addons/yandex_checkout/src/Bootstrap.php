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

namespace Tygh\Addons\YandexCheckout;

use Tygh\Core\ApplicationInterface;
use Tygh\Core\BootstrapInterface;
use Tygh\Core\HookHandlerProviderInterface;

class Bootstrap implements BootstrapInterface, HookHandlerProviderInterface
{
    public function boot(ApplicationInterface $app)
    {
        $app->register(new ServiceProvider());
    }

    public function getHookHandlerMap()
    {
        $handlers_map = [
            'change_order_status_post' => [
                'addons.yandex_checkout.hook_handlers.orders',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\OrdersHookHandler::onChangeOrderStatusPost() */
                'onChangeOrderStatusPost',
            ],
            'user_init' => [
                'addons.yandex_checkout.hook_handlers.init',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\InitHookHandler::onUserInit() */
                'onUserInit',
            ],
            'get_payment_processors_post' => [
                'addons.yandex_checkout.hook_handlers.payments',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\PaymentsHookHandler::onGetPaymentProcessorsPost() */
                'onGetPaymentProcessorsPost'
            ],
            'rma_update_details_post' => [
                'addons.yandex_checkout.hook_handlers.rma',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\RmaHookHandler::onRmaUpdateDetailsPost() */
                'onRmaUpdateDetailsPost'
            ],
            'save_log' => [
                'addons.yandex_checkout.hook_handlers.logs',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\LoggingHookHandler::onSaveLog() */
                'onSaveLog'
            ]
        ];

        if (fn_allowed_for('MULTIVENDOR')) {
            $handlers_map['get_company_data'] = [
                'addons.yandex_checkout.hook_handlers.companies',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\CompaniesHookHandler::onGetCompanyData() */
                'onGetCompanyData'
            ];

            $handlers_map['prepare_checkout_payment_methods_before_get_payments'] = [
                'addons.yandex_checkout.hook_handlers.payments',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\PaymentsHookHandler::onBeforeGetPayments() */
                'onBeforeGetPayments'
            ];

            $handlers_map['get_payments'] = [
                'addons.yandex_checkout.hook_handlers.payments',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\PaymentsHookHandler::onGetPayments() */
                'onGetPayments'
            ];

            $handlers_map['prepare_checkout_payment_methods_after_get_payments'] = [
                'addons.yandex_checkout.hook_handlers.payments',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\PaymentsHookHandler::onAfterGetPayments() */
                'onAfterGetPayments'
            ];

            $handlers_map['vendor_plans_calculate_commission_for_payout_before'] = [
                'addons.yandex_checkout.hook_handlers.vendor_plans',
                /** @see \Tygh\Addons\YandexCheckout\HookHandlers\VendorPlansHookHandler::onBeforePayouts() */
                'onBeforePayouts'
            ];
        }

        return $handlers_map;
    }

}