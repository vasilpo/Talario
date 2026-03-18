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

namespace Tygh\Addons\PaypalCheckout\Exception;

use Tygh\Exceptions\AException;

class ApiException extends AException
{
    /**
     * @var array<array<string, string>>
     */
    protected $details;

    /**
     * Sets exception details.
     *
     * @param array<array<string, string>> $details Exception details
     *
     * @return void
     */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }

    /**
     * Gets exception details.
     *
     * @return array<array<string, string>>
     */
    public function getDetails()
    {
        return $this->details;
    }
}
