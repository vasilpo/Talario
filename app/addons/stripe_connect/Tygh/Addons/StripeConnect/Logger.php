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

namespace Tygh\Addons\StripeConnect;

use Stripe\Util\LoggerInterface;
use Exception;

class Logger implements LoggerInterface
{
    const LOG_TYPE = 'stripe_connect';
    const ACTION_FAILURE = 'sc_failure';
    const ACTION_WARNING = 'sc_warning';
    const ACTION_INFO = 'sc_info';

    /**
     * Gets available actions
     *
     * @return string[]
     */
    public static function getActions()
    {
        return [
            self::ACTION_FAILURE,
            self::ACTION_WARNING,
            self::ACTION_INFO
        ];
    }

    /**
     * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     *
     * Basic logging method
     *
     * @param string                  $action  Log action
     * @param string                  $message Log message
     * @param array<array-key, mixed> $context Context
     *
     * @return void
     */
    public static function log($action, $message, array $context = [])
    {
        $data = [
            'message' => strip_tags($message)
        ];

        if (!empty($context)) {
            $data['context'] = var_export($context, true);
        }

        fn_log_event(self::LOG_TYPE, $action, $data);
    }

    /**
     * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     *
     * Logs message of exception and StripeException context data
     *
     * @param Exception               $e       Exception
     * @param array<array-key, mixed> $context Context
     */
    public static function logException(Exception $e, array $context = [])
    {
        if ($e instanceof StripeException) {
            $context = array_merge($e->getContext(), $context);
        }

        self::log(self::ACTION_FAILURE, $e->getMessage(), $context);
    }

    /**
     * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     *
     * Logs StripeConnect library errors
     *
     * @param string                  $message Error message
     * @param array<array-key, mixed> $context Context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        self::log(self::ACTION_FAILURE, $message, $context);
    }
}
