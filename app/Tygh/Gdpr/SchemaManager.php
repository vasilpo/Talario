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

namespace Tygh\Gdpr;

/**
 * Provides methods to work with GDPR schema
 *
 * @package Tygh\Gdpr
 */
class SchemaManager
{
    /** @var array $schemas Cached schemas array */
    protected $schemas = [];

    /**
     * @param string $name Schema name.
     *
     * @return array<string, string|array>
     */
    public function getSchema($name)
    {
        if (!isset($this->schemas[$name])) {
            $this->schemas[$name] = fn_get_schema('gdpr', $name);
        }

        return $this->schemas[$name];
    }
}
