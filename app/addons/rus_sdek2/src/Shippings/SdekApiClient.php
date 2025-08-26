<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\RusSdek2\Shippings;

use Http\Adapter\Guzzle7\Client;
use CdekSDK2\BaseTypes\Intake;
use CdekSDK2\BaseTypes\Invoice;
use CdekSDK2\BaseTypes\Order;
use CdekSDK2\BaseTypes\OrdersList;
use CdekSDK2\Client as CdekSDK2Client;
use CdekSDK2\Dto\OrderInfo;
use CdekSDK2\Dto\PickupPointList;
use CdekSDK2\Exceptions\AuthException;
use CdekSDK2\Exceptions\RequestException;
use Tygh\Enum\YesNo;
use Tygh\Http;

class SdekApiClient
{
    /** @var \Http\Adapter\Guzzle7\Client */
    protected $client;

    /** @var \CdekSDK2\Client */
    protected $cdek;

    /** @var string */
    private $api_url = 'https://api.cdek.ru/v2/';

    /** @var string */
    private $access_token = '';

    /** @var string */
    private $authlogin = '';

    /** @var string */
    private $authpassword = '';

    /** @var int */
    private $sdek_timeout = 5;

    /**
     * SdekApiClient class constructor.
     *
     * @param array $shipping_params Shipping service params
     *
     * @return void
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function __construct(array $shipping_params = [])
    {
        $this->client = new Client();
        $this->authlogin = $shipping_params['authlogin'] ?? '';
        $this->authpassword = $shipping_params['authpassword'] ?? '';

        $this->cdek = new CdekSDK2Client($this->client, $this->authlogin, $this->authpassword);

        try {
            if (YesNo::toBool($shipping_params['test_mode'])) {
                $this->api_url = 'https://api.edu.cdek.ru/v2/';
                $this->cdek->setTest(true);
            }

            $this->cdek->authorize();
            $this->access_token = $this->cdek->getToken();
        } catch (AuthException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates SDEK order.
     *
     * @param \CdekSDK2\BaseTypes\Order $sdek_order Prepared order data array
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function createOrder(Order $sdek_order)
    {
        try {
            $result = $this->cdek->orders()->add($sdek_order);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $cdek_order = $this->cdek->formatResponse($result, Order::class);

                if (empty($cdek_order->entity->uuid)) {
                    throw new \Exception(__('rus_sdek2.uuid_not_found'));
                }

                return $cdek_order->entity->uuid;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Gets SDEK order tracking number.
     *
     * @param string $order_uuid Order UUID
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getSdekOrderTrackingNumber($order_uuid)
    {
        try {
            $result = $this->cdek->orders()->get($order_uuid);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $response_order = $this->cdek->formatResponse($result, OrderInfo::class);

                if (empty($response_order->entity->cdek_number)) {
                    throw new \Exception(__('rus_sdek2.cdek_number_not_found'));
                }

                return $response_order->entity->cdek_number;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Gets SDEK order by UUID.
     *
     * @param string $uuid Order UUID
     *
     * @return OrderInfo
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getOrder($uuid)
    {
        try {
            $result = $this->cdek->orders()->get($uuid);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $response_order = $this->cdek->formatResponse($result, OrderInfo::class);

                if (empty($response_order->entity)) {
                    throw new \Exception(__('rus_sdek2.entity_not_found'));
                }

                return $response_order->entity;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Deletes SDEK order by UUID.
     *
     * @param string $uuid Order UUID
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function deleteOrder($uuid)
    {
        try {
            $result = $this->cdek->orders()->delete($uuid);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $response_order = $this->cdek->formatResponse($result, Order::class);

                if (empty($response_order->entity->uuid)) {
                    throw new \Exception(__('rus_sdek2.uuid_not_found'));
                }

                return $response_order->entity->uuid;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates SDEK invoice.
     *
     * @param string $order_uuid Order UUID
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function createInvoice($order_uuid)
    {
        $invoice = Invoice::create([
            'orders' => [
                OrdersList::create([
                    'order_uuid' => $order_uuid
                ]),
            ],
            'copy_count' => 1,
        ]);

        try {
            $result = $this->cdek->invoice()->add($invoice);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $response = $this->cdek->formatResponse($result, Invoice::class);

                if (empty($response->entity->uuid)) {
                    throw new \Exception(__('rus_sdek2.uuid_not_found'));
                }

                return $response->entity->uuid;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Downloads invoice in PDF format.
     *
     * @param string $invoice_uuid Invoice UUID
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function downloadInvoice($invoice_uuid)
    {
        try {
            $result = $this->cdek->invoice()->download($invoice_uuid);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                if (empty($result->getBody())) {
                    throw new \Exception(__('rus_sdek2.empty_response_body'));
                }

                return $result->getBody();
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates SDEK courier call request.
     *
     * @param \CdekSDK2\BaseTypes\Intake $intake_data Courier call data
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function createCallCourier(Intake $intake_data)
    {
        try {
            $result = $this->cdek->intakes()->add($intake_data);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $response = $this->cdek->formatResponse($result, Intake::class);

                if (empty($response->entity->uuid)) {
                    throw new \Exception(__('rus_sdek2.uuid_not_found'));
                }

                return $response->entity->uuid;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates SDEK schedule. SDEK SDK method not used here because there is no such method.
     *
     * @param array $schedule Schedule data
     *
     * @return string
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function createSchedule(array $schedule)
    {
        $response = Http::post(
            $this->api_url . 'delivery',
            json_encode($schedule),
            [
                'timeout' => $this->sdek_timeout,
                'headers' => [
                    'Authorization: Bearer ' . $this->access_token,
                    'Content-Type: application/json'
                ]
            ]
        );

        $response_arr = json_decode($response, true);

        if (empty($response_arr)) {
            throw new \Exception(__('rus_sdek2.request_error'));
        }

        if (!empty($response_arr['error'])) {
            throw new \Exception($response_arr['error']);
        }

        if (!empty($response_arr['requests'][0]['errors'])) {
            throw new \Exception($response_arr['requests'][0]['errors'][0]['message']);
        }

        if (empty($response_arr['entity']['uuid'])) {
            throw new \Exception(__('rus_sdek2.uuid_not_found'));
        }

        return $response_arr['entity']['uuid'];
    }

    /**
     * Gets SDEK delivery points list.
     *
     * @param array $params Extra params for request
     *
     * @return array|null
     *
     * @throws \Exception Exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getDeliveryPoints(array $params)
    {
        try {
            $result = $this->cdek->offices()->getFiltered($params);

            if ($result->hasErrors()) {
                throw new \Exception($result->getErrors()[0]['message']);
            }

            if ($result->isOk()) {
                $pvzlist = $this->cdek->formatResponseList($result, PickupPointList::class);

                $delivery_points = [];
                foreach ($pvzlist->items as $pvz) {
                    $delivery_points[$pvz->code] = (array) $pvz;
                    $delivery_points[$pvz->code]['location'] = (array) $pvz->location;
                    if (!empty($pvz->office_image_list)) {
                        foreach ($pvz->office_image_list as $office_image) {
                            $delivery_points[$pvz->code]['office_image_list'] = (array) $office_image;
                        }
                    }
                    if (!empty($pvz->work_time_list)) {
                        foreach ($pvz->work_time_list as $work_time) {
                            $delivery_points[$pvz->code]['work_time_list'] = (array) $work_time;
                        }
                    }
                    if (!empty($pvz->work_time_exceptions)) {
                        foreach ($pvz->work_time_exceptions as $work_time_exception) {
                            $delivery_points[$pvz->code]['work_time_exceptions'] = (array) $work_time_exception;
                        }
                    }
                    if (empty($pvz->phones)) {
                        continue;
                    }
                    foreach ($pvz->phones as $phone) {
                        $delivery_points[$pvz->code]['phones'] = (array) $phone;
                    }
                }

                return $delivery_points;
            }
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }
    }
}
