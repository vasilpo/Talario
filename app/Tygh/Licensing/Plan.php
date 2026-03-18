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

namespace Tygh\Licensing;

class Plan
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array<string, object>
     */
    private $feature_collection;

    /**
     * @param string                $key                Key
     * @param array<string, object> $feature_collection Feature collection
     */
    public function __construct($key, array $feature_collection)
    {
        $this->key = $key;
        $this->feature_collection = $feature_collection;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * @return object[]
     */
    public function getFeatureCollection()
    {
        return $this->feature_collection;
    }
}
