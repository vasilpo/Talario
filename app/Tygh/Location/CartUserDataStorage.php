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

namespace Tygh\Location;

use Tygh\Tygh;

/**
 * Class CartUserDataStorage provides a user data storage that modifies the cart object that is stored in the current session.
 *
 * @package Tygh\Location
 */
class CartUserDataStorage implements IUserDataStorage
{
    /**
     * @var array
     */
    protected $storage;

    public function __construct($storage = null)
    {
        if ($storage === null) {
            $this->storage = &Tygh::$app['session']['cart']['user_data'];
        } else {
            $this->storage = $storage;
        }
    }

    /** @inheritdoc */
    public function getAll()
    {
        return $this->storage;
    }

    /** @inheritdoc */
    public function get($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }

        return null;
    }

    /** @inheritdoc */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /** @inheritdoc */
    public function delete($key)
    {
        unset($this->storage[$key]);
    }
}
