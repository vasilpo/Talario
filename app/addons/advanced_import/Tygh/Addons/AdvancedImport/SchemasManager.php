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

namespace Tygh\Addons\AdvancedImport;

class SchemasManager
{
    protected $schemas = array();

    /**
     * Provides relations schema.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->get('relations');
    }

    /**
     * Provides modifiers schema.
     *
     * @return array
     */
    public function getModifiers()
    {
        return $this->get('modifiers');
    }

    /**
     * Provides products schema.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->get('products');
    }

    /**
     * Gets add-on schema.
     *
     * @param string $schema Schema name
     *
     * @return array
     */
    protected function get($schema)
    {
        if (!isset($this->schemas[$schema])) {
            $this->schemas[$schema] = fn_get_schema('advanced_import', $schema);
        }

        return $this->schemas[$schema];
    }
}