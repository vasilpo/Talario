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

namespace Tygh\Addons\RusOnlineCashRegister\CashRegister\Atol;


use Tygh\Addons\RusOnlineCashRegister\CashRegister\Response;

/**
 * The response class represents request response on authorizing user.
 *
 * @package Tygh\Addons\RusOnlineCashRegister\CashRegister\Atol
 */
class TokenResponse extends Response
{
    /** @var string|null */
    protected $token;

    /**
     * TokenResponse constructor.
     *
     * @param string $response Raw response string.
     */
    public function __construct($response)
    {
        $data = @json_decode($response, true);

        if (json_last_error()) {
            $this->setError(json_last_error(), json_last_error_msg());
        } elseif (!is_array($data)) {
            $this->setError('internal', 'Response json is invalid');
        } else {
            if (isset($data['error'])) {
                $this->setError($data['error']['code'], $data['error']['text']);
            } elseif (isset($data['code']) && $data['code'] >= 2) {
                // Backward compatible for API v3
                // If the error code is 0, then the auth token issued.
                // If the error code is 1, then the old auth token issued.
                // If the error code is greater than or equal to 2, the authorization failed.

                $this->setError($data['code'], $data['text']);
            } else {
                $this->token = $data['token'];
            }
        }
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }
}
