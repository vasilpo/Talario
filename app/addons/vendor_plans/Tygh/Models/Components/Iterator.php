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

namespace Tygh\Models\Components;

class Iterator implements \Iterator
{
    /**
     * @var array the data to be iterated through
     */
    private $data;

    /**
     * @var array list of keys in the map
     */
    private $keys;

    /**
     * @var mixed current key
     */
    private $key;

    /**
     * Constructor.
     *
     * @param array $data the data to be iterated through
     */
    public function __construct(&$data)
    {
        $this->data = &$data;
        $this->keys = array_keys($data);
        $this->key = reset($this->keys);
    }

    /**
     * Rewinds internal array pointer.
     * This method is required by the interface Iterator.
     */
    public function rewind()
    {
        $this->key = reset($this->keys);
    }

    /**
     * Returns the key of the current array element.
     * This method is required by the interface Iterator.
     *
     * @return mixed the key of the current array element
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Returns the current array element.
     * This method is required by the interface Iterator.
     *
     * @return mixed the current array element
     */
    public function current()
    {
        return $this->data[$this->key];
    }

    /**
     * Moves the internal pointer to the next array element.
     * This method is required by the interface Iterator.
     */
    public function next()
    {
        $this->key = next($this->keys);
    }

    /**
     * Returns whether there is an element at current position.
     * This method is required by the interface Iterator.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->key !== false;
    }
}
