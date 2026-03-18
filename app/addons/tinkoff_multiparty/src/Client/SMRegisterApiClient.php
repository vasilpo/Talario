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

namespace Tygh\Addons\TinkoffMultiparty\Client;

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Enum\NotificationSeverity;
use Tygh\Http;
use Tygh\Registry;

/**
 * Class contains methods for sending requests for SM-Register T-Bank API.
 */
class SMRegisterApiClient
{
    /** @var string $endpoint */
    protected $endpoint = 'https://acqapi.tinkoff.ru/';

    /** @var string $username */
    protected $username;

    /** @var string $password */
    protected $password;

    /** @var string $certificate_filename */
    protected $certificate_filename;

    /** @var string $certificate_key_filename */
    protected $certificate_key_filename;

    /**
     * Constructor of EACQMultipartyClient class.
     *
     * @param string $username                 Username
     * @param string $password                 Password
     * @param string $certificate_filename     MTLS certificate path
     * @param string $certificate_key_filename Certificate private key path
     */
    public function __construct($username, $password, $certificate_filename, $certificate_key_filename)
    {
        $this->username = $username;
        $this->password = $password;
        $this->certificate_filename = $certificate_filename;
        $this->certificate_key_filename = $certificate_key_filename;
    }

    /**
     * Registers ShopCode.
     *
     * @param array<string, string> $request_body Current request request body data.
     *
     * @return array<string, array<string, string>>
     */
    public function registerShopCode(array $request_body)
    {
        $path = 'sm-register/register';
        $method = Http::POST;

        $authorization_body = [
            'username' => $this->username,
            'password' => $this->password,
        ];
        $access_token = $this->getOAuthToken($authorization_body);
        $extra = [
            'Authorization: Bearer ' . $access_token,
        ];

        $response = $this->executeRegister($path, $method, $request_body, $extra);
        if (!empty($response['errors'])) {
            /** @var array<string, string> $error */
            foreach ($response['errors'] as $error) {
                fn_set_notification(
                    NotificationSeverity::ERROR,
                    __('error'),
                    $error['field'] . ' (' . $error['rejectedValue'] . ') ' . $error['defaultMessage']
                );
            }
        }

        /** @psalm-suppress InvalidReturnStatement */
        return $response;
    }

    /**
     * Gets OAuth token.
     *
     * @param array<string, string> $request_body Current request body data.
     */
    public function getOAuthToken(array $request_body = []): string
    {
        $path = 'oauth/token';
        $method = Http::POST;
        $request_body = array_merge([
            'grant_type' => 'password',
            'username' => 'login',
            'password' => 'password',
        ], $request_body);

        $basic_auth = 'partner:partner';
        $extra = ['Authorization: Basic ' . base64_encode($basic_auth)];

        $allow_logging = Registry::ifGet('allow_logging_basic_authorization_sm', false);
        $logging = Http::$logging;
        if ($allow_logging) {
            Http::$logging = false;
        }

        $response = $this->execute($path, $method, $request_body, $extra);

        if ($allow_logging) {
            Http::$logging = $logging;
        }

        return !empty($response['access_token'])
            ? $response['access_token']
            : '';
    }

    /**
     * Makes request to Basic Auth SM-Register API endpoint.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @param string                   $path    Name of request method at SM-Register T-Bank API.
     * @param string                   $method  Type of request to SM-Register T-Bank API.
     * @param array<string, string>    $params  Encoded body of request.
     * @param array<array-key, string> $headers Request headers.
     *
     * @return array<string, string>|string
     */
    protected function execute($path, $method, array $params, array $headers = [])
    {
        $headers = array_merge([
            'Content-Type: application/x-www-form-urlencoded',
        ], $headers);
        switch ($method) {
            case Http::GET:
                $answer = Http::get($this->endpoint . $path, $params, ['headers' => $headers]);
                break;
            case Http::POST:
                $answer = Http::post(
                    $this->endpoint . $path,
                    http_build_query($params),
                    [
                        'headers' => $headers,
                        'ssl_cert' => $this->certificate_filename,
                        'ssl_key' => $this->certificate_key_filename,
                    ]
                );
                break;
            default:
                $answer = '';
                break;
        }
        $answer = json_decode($answer, true);
        return $answer;
    }

    /**
     * Makes request to SM-Register API endpoint.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @param string                   $path    Name of request method at SM-Register T-Bank API.
     * @param string                   $method  Type of request to SM-Register T-Bank API.
     * @param array<string, string>    $params  Encoded body of request.
     * @param array<array-key, string> $headers Request headers.
     *
     * @return array<string, array<string, string>>
     */
    protected function executeRegister($path, $method, array $params, array $headers = [])
    {
        $headers = array_merge([
            'Content-Type: application/json',
        ], $headers);
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);

        switch ($method) {
            case Http::GET:
                $answer = Http::get($this->endpoint . $path, $params, ['headers' => $headers]);
                break;
            case Http::POST:
                $answer = Http::post(
                    $this->endpoint . $path,
                    $params,
                    [
                        'headers' => $headers,
                        'ssl_cert' => $this->certificate_filename,
                        'ssl_key' => $this->certificate_key_filename,
                    ]
                );
                break;
            default:
                $answer = '';
                break;
        }

        return json_decode($answer, true);
    }

    /**
     * Handling error response.
     *
     * @param array<string, string> $response Response from T-Bank API.
     *
     * @return void
     */
    public function handleError(array $response)
    {
        fn_set_notification(NotificationSeverity::ERROR, $response['Message'], $response['Details']);
    }
}
