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

namespace Tygh\AuthorizeNetApi;

use net\authorize\api\contract\v1 as AnetAPI;
use Tygh\AuthorizeNetApi\Client\Client;
use Tygh\AuthorizeNetApi\Transaction\Transaction;
use Tygh\Enum\OrderStatuses;
use Tygh\Registry;

/**
 * Class for interacting with the Authorize.Net API.
 */
class AuthorizeNet
{
    const TEST_URL = 'https://apitest.authorize.net/xml/v1/request.api';
    const LIVE_URL = 'https://api.authorize.net/xml/v1/request.api';
    const SUCCESS_RESULT_CODE = 'Ok';
    const SUCCESS_RESPONSE_CODE = '1';

    /** @var Client */
    protected $client;

    /** @var string */
    protected $url;

    /** @var Transaction */
    protected $transaction;

    /**
     * Constructor.
     *
     * @param array<array<string, string|int|bool>>               $processor_data Payment processor data.
     * @param array<string, string|int|array<string, string|int>> $order_info     Order information.
     */
    public function __construct(array $processor_data, array $order_info)
    {
        $this->client = new Client($processor_data);
        $this->url = ($processor_data['processor_params']['mode'] === 'test')
            ? self::TEST_URL
            : self::LIVE_URL;

        $this->transaction = new Transaction($processor_data, $order_info);
    }

    /**
     * Sends a transaction request to Authorize.Net.
     *
     * @return AnetAPI\CreateTransactionResponse
     */
    public function sendTransaction()
    {
        $transaction_data = $this->transaction->buildTransactionData();
        Registry::set('log_cut_data', ['cardNumber', 'expirationDate', 'cardCode']);

        return $this->client->sendRequest($this->url, $transaction_data);
    }

    /**
     * Processes the API response.
     *
     * @param AnetAPI\CreateTransactionResponse $response API response object.
     *
     * @return array{string|null, string|null}
     */
    public function processResponse(AnetAPI\CreateTransactionResponse $response)
    {
        if ($response !== null && $response->getMessages()->getResultCode() === self::SUCCESS_RESULT_CODE) {
            $transaction_response = $response->getTransactionResponse();

            if (
                $transaction_response !== null
                && $transaction_response->getResponseCode() === self::SUCCESS_RESPONSE_CODE
                && $transaction_response->getMessages() !== null
            ) {
                $transaction_id = $transaction_response->getTransId();
                $order_status = $this->getOrderStatusByTransactionType();

                return [$transaction_id, $order_status];
            }
        }

        return [null, null];
    }

    /**
     * Determines the order status based on the transaction type.
     *
     * @return string
     */
    public function getOrderStatusByTransactionType()
    {
        return $this->transaction->transaction_type === 'A' ? OrderStatuses::OPEN : OrderStatuses::PAID;
    }
}
