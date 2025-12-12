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

namespace Tygh\AuthorizeNetApi\Client;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

/**
 * Client for interacting with the Authorize.Net API.
 */
class Client
{
    /** @var AnetAPI\MerchantAuthenticationType */
    protected $merchant_auth;

    /**
     * Constructor.
     *
     * @param array<array<string, string|int|bool>> $processor_data Payment processor data.
     *
     * @psalm-suppress InvalidScalarArgument
     */
    public function __construct(array $processor_data)
    {
        $merchant_auth = new AnetAPI\MerchantAuthenticationType();
        $merchant_auth->setName($processor_data['processor_params']['login']);
        $merchant_auth->setTransactionKey($processor_data['processor_params']['transaction_key']);

        $this->merchant_auth = $merchant_auth;
    }

    /**
     * Sends a transaction request to Authorize.Net.
     *
     * @param string                         $url          API endpoint URL.
     * @param AnetAPI\TransactionRequestType $request_data Request data.
     *
     * @return AnetAPI\CreateTransactionResponse
     *
     * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
     */
    public function sendRequest($url, AnetAPI\TransactionRequestType $request_data)
    {
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchant_auth);
        $request->setTransactionRequest($request_data);
        $controller = new AnetController\CreateTransactionController($request);

        $response = $controller->executeWithApiResponse($url);

        fn_log_event('requests', 'http', [
            'url' => $url,
            'data' => var_export($request->getTransactionRequest(), true),
            'response' => var_export($controller->getApiResponse(), true),
        ]);

        return $response;
    }
}
