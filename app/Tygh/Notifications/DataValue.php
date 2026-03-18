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

namespace Tygh\Notifications;

use Tygh\Registry;

/**
 * Class DataValue
 *
 * @package Tygh\Notifications
 */
class DataValue
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $default_value;

    /**
     * DataValue constructor.
     *
     * @param string $key
     * @param mixed  $default_value
     */
    public function __construct($key, $default_value = null)
    {
        $this->key = $key;
        $this->default_value = $default_value;
    }

    /**
     * @param string $key
     * @param mixed  $default_value
     *
     * @return \Tygh\Notifications\DataValue
     */
    public static function create($key, $default_value = null)
    {
        return new self($key, $default_value);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }
}