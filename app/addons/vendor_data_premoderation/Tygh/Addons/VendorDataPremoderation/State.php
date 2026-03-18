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

namespace Tygh\Addons\VendorDataPremoderation;

use Tygh\Exceptions\DeveloperException;

/**
 * Class State stores an object state collected from multiple data sources.
 *
 * @package Tygh\Addons\VendorDataPremoderation
 */
class State
{
    /**
     * @var array
     */
    protected $state;

    public function __construct(array $state)
    {
        $this->state = $state;
    }

    /**
     * Gets list of source identifiers that store an object data.
     *
     * @return array
     */
    public function getSources()
    {
        return array_keys($this->state);
    }

    /**
     * Gets all data from the source.
     *
     * @param string $source_name
     *
     * @return mixed
     */
    public function getSourceData($source_name)
    {
        if (!isset($this->state[$source_name])) {
            throw new DeveloperException("{$source_name} is not present in the object state");
        }

        return $this->state[$source_name];
    }

    /**
     * Gets fields of data items stored in the source.
     *
     * @param string $source_name
     *
     * @return array
     */
    public function getSourceSchema($source_name)
    {
        $source = $this->getSourceData($source_name);
        if (!$source) {
            return [];
        }

        $item = reset($source);

        return array_keys($item);
    }

    /**
     * Return state array
     *
     * @return array<string, array<string, string|int>>
     */
    public function toArray()
    {
        return $this->state;
    }
}
