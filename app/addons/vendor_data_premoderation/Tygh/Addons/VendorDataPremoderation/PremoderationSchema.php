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
 * Class PremoderationSchema represents a set of object premoderation rules.
 *
 * @package Tygh\Addons\VendorDataPremoderation
 */
class PremoderationSchema
{
    /**
     * @var array
     */
    protected $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Gets data source premoderation rules.
     *
     * @param string $source_name
     *
     * @return bool|string[]
     */
    public function getSourcePremoderation($source_name)
    {
        if (!isset($this->schema[$source_name]['requires_premoderation'])) {
            return true;
        }

        return $this->schema[$source_name]['requires_premoderation'];
    }

    /**
     * Gets source data field premoderation rules.
     *
     * @param string $source_name
     * @param string $field_name
     *
     * @return bool|string[]
     */
    public function getFieldPremoderation($source_name, $field_name)
    {
        if ($this->getSourcePremoderation($source_name) === false) {
            return false;
        }

        if (!isset($this->schema[$source_name]['fields'][$field_name]['requires_premoderation'])) {
            return true;
        }

        return $this->schema[$source_name]['fields'][$field_name]['requires_premoderation'];
    }
}

