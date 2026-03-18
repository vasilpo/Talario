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


namespace Tygh\Addons\ProductVariations\Tools;


use Tygh\Database\Connection;

/**
 * Class QueryFactory
 *
 * @package Tygh\Addons\ProductVariations\Tools
 */
class QueryFactory
{
    protected $db_connection;

    public function __construct(Connection $db_connection)
    {
        $this->db_connection = $db_connection;
    }

    public function createQuery($table_id, array $conditions = [], array $fields = [], $table_alias = null)
    {
        if (is_array($table_id)) {
            $table_alias = reset($table_id);
            $table_id = key($table_id);
        }

        $query = new Query($this->db_connection, $table_id, $table_alias);

        if ($conditions) {
            $query->addConditions($conditions);
        }

        if ($fields) {
            $query->setFields($fields);
        }

        return $query;
    }
}