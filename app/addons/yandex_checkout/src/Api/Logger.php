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

namespace Tygh\Addons\YandexCheckout\Api;

use Exception;
use Psr\Log\LogLevel;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\LoggerWrapper;

/**
 * Class Logger allows to log any type of network activity.
 *
 * @package Tygh\Addons\YandexCheckout\Api
 */
class Logger
{
    const LOG_TYPE = 'yandex_checkout';

    const ACTION_FAILURE = 'yc_failed';

    const ACTION_REQUEST = 'yc_request';

    /** @var LoggerWrapper|null */
    private $logger_wrapper = null;

    /**
     * Provides logger to use in the API client.
     *
     * @return \YooKassa\Common\LoggerWrapper
     */
    public function getApiClientLoggerWrapper()
    {
        if ($this->logger_wrapper === null) {
            $this->logger_wrapper = new LoggerWrapper(
                static function ($level, $message, array $context = []) {
                    switch ($level) {
                        case LogLevel::INFO:
                            fn_log_event(
                                self::LOG_TYPE,
                                self::ACTION_REQUEST,
                                ['message' => $message, 'context' => $context]
                            );
                            break;
                        default:
                            fn_log_event(
                                self::LOG_TYPE,
                                self::ACTION_FAILURE,
                                ['message' => $message, 'context' => $context]
                            );
                            break;
                    }
                }
            );
        }

        return $this->logger_wrapper;
    }

    /**
     * Logs exception that was thrown when working with API and/or performing payment-related operations.
     *
     * @param \Exception $exception Thrown exception
     *
     * @return void
     */
    public function logException(Exception $exception)
    {
        if ($exception instanceof ApiException) {
            $this->getApiClientLoggerWrapper()->error(
                $exception->getMessage(),
                ['_headers' => $exception->getResponseHeaders(), '_body' => $exception->getResponseBody()]
            );
        } else {
            $this->getApiClientLoggerWrapper()->error(
                $exception->getMessage(),
                ['_trace' => $exception->getTraceAsString()]
            );
        }
    }
}
