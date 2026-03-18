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

/**
 * Class Diff stores a set of changed object data sources.
 *
 * @package Tygh\Addons\VendorDataPremoderation
 */
class Diff
{
    /**
     * @var array<string, bool>
     */
    protected $diff = [];

    /**
     * @var array<string, array<string, bool>>
     */
    protected $fields = [];

    /**
     * Adds changed data source.
     *
     * @param string $source_name
     */
    public function addChangedSource($source_name)
    {
        $this->diff[$source_name] = true;
    }

    /**
     * Adds changed data field.
     *
     * @param string $source_name Source name
     * @param string $field_name  Field name
     *
     * @return void
     */
    public function addChangedField($source_name, $field_name)
    {
        $this->diff[$source_name] = true;
        $this->fields[$source_name][$field_name] = true;
    }

    /**
     * Checks whether there are changed sources.
     *
     * @return bool
     */
    public function hasChanges()
    {
        return count($this->diff) > 0 || count($this->fields) > 0;
    }

    /**
     * Gets list of changed sources.
     *
     * @return array
     */
    public function getChangedSources()
    {
        return array_keys($this->diff);
    }

    /**
     * Return diff array
     *
     * @return array<string, bool>
     */
    public function getSources()
    {
        return $this->diff;
    }

    /**
     * Return fields array
     *
     * @return array<string, array<string, bool>>
     */
    public function getFields()
    {
        return $this->fields;
    }
}
