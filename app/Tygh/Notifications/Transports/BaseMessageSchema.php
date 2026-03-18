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

namespace Tygh\Notifications\Transports;

use Tygh\Notifications\Data;
use Tygh\Notifications\DataValue;

abstract class BaseMessageSchema
{
    /**
     * @var callable|null
     */
    public $data_modifier = null;

    /**
     * @var array
     */
    public $data = [];

    public function init(Data $data)
    {
        $self = clone $this;

        if ($this->data_modifier && is_callable($this->data_modifier)) {
            $data = new Data(call_user_func($this->data_modifier, $data->toArray()));
        }

        foreach (get_object_vars($self) as $var => $value) {
            if ($value instanceof DataValue) {
                $self->{$var} = $this->retrieveDataValue($value, $data);
            }
        }

        $self->data = $data->toArray();

        return $self;
    }

    protected function retrieveDataValue(DataValue $value, Data $data)
    {
        return $data->get($value->getKey(), $value->getDefaultValue());
    }

    protected static function get(array $data, $key, $default_value = null)
    {
        return array_key_exists($key, $data) ? $data[$key] : $default_value;
    }
}
