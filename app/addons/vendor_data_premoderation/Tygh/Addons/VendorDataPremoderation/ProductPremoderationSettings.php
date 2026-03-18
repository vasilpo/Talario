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
 * Class ProductPremoderationSettings checks whether the fields configured via the "Require approval for updates of"
 * add-on setting require premoderation.
 *
 * @package Tygh\Addons\VendorDataPremoderation
 */
class ProductPremoderationSettings implements PremoderationSettingsInterface
{
    /**
     * @var bool[]
     */
    protected $fields = [];

    /**
     * ProductPremoderationSettings constructor.
     *
     * @param array $fields Premoderated product fields
     */
    public function __construct(array $fields)
    {
        $this->fields = array_fill_keys($fields, true);
    }

    /**
     * Checks whether product field requires premoderation.
     *
     * @param string $field Field name
     *
     * @return bool
     */
    protected function isFieldPremoderationEnabled($field)
    {
        if (!isset($this->fields[$field])) {
            return false;
        }

        return $this->fields[$field];
    }

    /**
     * Checks whether modified product data field must be premoderated.
     *
     * @param string $source_name
     * @param string $field_name
     *
     * @return bool
     */
    public function getFieldPremoderation($source_name, $field_name)
    {
        return $this->isFieldPremoderationEnabled($source_name . self::SOURCE_FIELD_SEPARATOR . $field_name);
    }

    /**
     * Checks whether modified product data source must be premoderated.
     *
     * @param string $source_name
     *
     * @return bool
     */
    public function getSourcePremoderation($source_name)
    {
        return $this->getFieldPremoderation($source_name, self::ALL_FIELDS_SELECTOR);
    }
}
