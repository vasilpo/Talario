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


namespace Tygh\Mailer;

/**
 * Class SendResult
 * @package Tygh\Mailer
 */
class SendResult
{
    /** @var array  */
    private $errors = array();

    /** @var bool  */
    private $is_success = false;

    /**
     * SendResult constructor.
     *
     * @param bool  $is_success Success flag
     * @param array $errors     List of error messages
     */
    public function __construct($is_success = false, array $errors = array())
    {
        foreach ($errors as $error) {
            $this->setError($error);
        }

        $this->setIsSuccess($is_success);
    }

    /**
     * Get result of sending
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->is_success;
    }

    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set error message
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Set result of sending
     *
     * @param boolean $is_success
     */
    public function setIsSuccess($is_success)
    {
        $this->is_success = (bool) $is_success;
    }
}