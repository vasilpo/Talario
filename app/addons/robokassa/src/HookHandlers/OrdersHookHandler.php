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

namespace Tygh\Addons\Robokassa\HookHandlers;

use Tygh\Addons\Robokassa\Service;
use Tygh\Application;
use Tygh\Http;

/**
 * This class describes the hook handlers related to orders management
 *
 * @package Tygh\Addons\Robokassa\HookHandlers
 */
class OrdersHookHandler
{
    /** @var Application $application */
    protected $application;

    /**
     * OrdersHookHandler constructor.
     *
     * @param Application $application Application
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * The "change_order_status" hook handler.
     *
     * Actions performed:
     * - Sends the full receipt when the order status changes.
     *
     * @param string                                  $status_to   Status to
     * @param string                                  $status_from Status from
     * @param array<string|int|array<string, string>> $order_info  Order info
     *
     * @return void|null
     *
     * @see fn_change_order_status()
     */
    public function onChangeOrderStatus($status_to, $status_from, $order_info)
    {
        $processor_data = fn_get_processor_data($order_info['payment_id']);

        if (empty($processor_data['processor_script'])) {
            return null;
        }

        //phpcs:ignore
        if ($processor_data['processor_script'] === 'robokassa.php'
            && $status_to === $processor_data['processor_params']['statuses']['final']
            && isset($order_info['payment_info']['robokassa.prepayment_receipt_created'])
            && !isset($order_info['payment_info']['robokassa.payment_receipt_created'])
        ) {
            $url = 'https://ws.roboxchange.com/RoboFiscal/Receipt/Attach';

            $receipt = Service::getFullPaymentReceipt($processor_data, $order_info);
            if ($receipt) {
                $data = Service::encodeReceipt($receipt, $processor_data);

                $request = Http::post($url, $data);
                $request_answer = json_decode($request, true);
                //phpcs:ignore
                if (isset($request_answer['ResultCode']) && $request_answer['ResultCode'] == 0) {
                    fn_update_order_payment_info($order_info['order_id'], ['rus_payments.robokassa.payment_receipt_created' => __('yes')]);
                }
            }
        }
    }
}
