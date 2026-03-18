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


namespace Tygh\Addons\RusOnlineCashRegister\CashRegister;

/**
 * The base response class represents request response.
 *
 * @package Tygh\Addons\RusOnlineCashRegister\CashRegister
 */
class Response
{
    /** @var string[] */
    protected $errors = array();

    /** @var string */
    protected $status;

    /** @var string */
    protected $status_message;

    /** @var string|null */
    protected $uuid;

    /**
     * Gets receipt uuid.
     *
     * @return null|string
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * Sets receipt uuid.
     *
     * @param null|string $uuid
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Gets errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets error.
     *
     * @param string $code      Error code.
     * @param string $message   Error message.
     */
    public function setError($code, $message)
    {
        $this->errors[$code] = (string) $message;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Gets status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->status_message;
    }

    /**
     * @param string $status_message
     */
    public function setStatusMessage($status_message)
    {
        $this->status_message = $status_message;
    }
}