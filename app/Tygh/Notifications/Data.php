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

/**
 * Class Data gets and stores an array of data for a notification event.
 *
 * @package Tygh\Notifications
 */
class Data
{
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($key, $default_value = null)
    {
        if (strpos($key, '.') === false) {
            return array_key_exists($key, $this->data) ? $this->data[$key] : $default_value;
        }

        $data = $this->data;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default_value;
            }

            $data = &$data[$segment];
        }

        return $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}