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

/**
 * Class contains methods for sending requests for EACQ T-Bank API.
 */
class EACQApiClient
{
    /** @var string $endpoint */
    protected $endpoint = 'https://securepay.tinkoff.ru/v2/';

    const PROTOCOL_VERSION = '1.32';

    /**
     * Makes request to T-Bank API endpoint.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @param string                                      $path    Name of request method at T-Bank API.
     * @param string                                      $method  Type of request to T-Bank API.
     * @param array<string, string|array<string, string>> $params  Encoded body of request.
     * @param array<string, string>                       $headers Request headers.
     *
     * @return array<string, string>|string
     */
    protected function execute($path, $method, array $params, array $headers = [])
    {
        $headers = array_merge([
            'Content-type: application/json',
        ], $headers);
        $params = json_encode($params);
        switch ($method) {
            case Http::GET:
                $answer = Http::get($this->endpoint . $path, $params, ['headers' => $headers]);
                break;
            case Http::POST:
                $answer = Http::post($this->endpoint . $path, $params, ['headers' => $headers]);
                break;
            default:
                $answer = '';
                break;
        }
        $answer = json_decode($answer, true);
        return $answer;
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
        if (empty($response['Message']) || empty($response['Details'])) {
            fn_set_notification(NotificationSeverity::ERROR, __('error'), __('addons.tinkoff_multiparty.full_refunded.notification', [
                '[errorCode]' => $response['ErrorCode']
            ]));

            return;
        }

        fn_set_notification(NotificationSeverity::ERROR, $response['Message'], $response['Details']);
    }

    /**
     * Calculates request token.
     *
     * @param array<string, string|int|array<string, string|array<string, string>>> $request_body           Current request body state.
     * @param string                                                                $password               Terminal password.
     * @param array<string>                                                         $unsupported_parameters Fields of request which should not be part of token calculation.
     */
    public function generateRequestToken(array $request_body, $password, array $unsupported_parameters = []): string
    {
        $result = '';
        $filter_func = static function ($key, $value) use ($unsupported_parameters) {
            if (empty($unsupported_parameters)) {
                return is_array($value);
            }
            return in_array($key, $unsupported_parameters);
        };
        $request_body['Password'] = $password;
        ksort($request_body);
        foreach ($request_body as $request_parameter => $parameter_value) {
            if ($filter_func($request_parameter, $parameter_value)) {
                continue;
            }
            $result .= $parameter_value;
        }
        return (string) hash('sha256', $result);
    }
}
