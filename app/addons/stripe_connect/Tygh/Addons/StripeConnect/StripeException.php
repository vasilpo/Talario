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

use Exception;

class StripeException extends Exception
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint

    /** @var array<array-key, mixed> */
    private $context;

    /**
     * StripeException constructor.
     *
     * @param string                  $message Exception message
     * @param array<array-key, mixed> $context Additional context data
     */
    public function __construct($message, array $context = [])
    {
        parent::__construct($message);
        $this->context = $context;
    }

    /**
     * @return array<string, string>
     */
    public function getContext()
    {
        return $this->context;
    }
}
