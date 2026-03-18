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

namespace Tygh\Backend\Database;

/**
 * Interface IBackend
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 */
interface IBackend
{
    /**
     * Connects to database server.
     *
     * @param string $user     User name
     * @param string $passwd   Password
     * @param string $host     Server host name
     * @param string $database Database name
     *
     * @return bool True on success, false - otherwise
     */
    public function connect($user, $passwd, $host, $database);

    /**
     * Disconnects from the database.
     */
    public function disconnect();

    /**
     * Changes current database.
     *
     * @param string $database Database name
     *
     * @return bool True on success, false - otherwise
     */
    public function changeDb($database);

    /**
     * Queries database.
     *
     * @param string $query SQL query
     *
     * @return mixed Query result
     */
    public function query($query);

    /**
     * Fetches row from query result set.
     *
     * @param mixed  $result Result set
     * @param string $type   Fetch type - 'assoc' or 'indexed'
     *
     * @return array<array-key, mixed> Fetched data
     */
    public function fetchRow($result, $type = 'assoc');

    /**
     * Frees result set.
     *
     * @param mixed $result Result set
     */
    public function freeResult($result);

    /**
     * Return number of rows affected by query.
     *
     * @param mixed $result Result set
     *
     * @return int Number of rows
     */
    public function affectedRows($result);

    /**
     * Returns last value of auto increment column.
     *
     * @return int Value
     */
    public function insertId();

    /**
     * Gets last error code.
     *
     * @return int Error code
     */
    public function errorCode();

    /**
     * Gets last error description.
     *
     * @return string Error description
     */
    public function error();

    /**
     * Escapes value.
     *
     * @param mixed $value Value to escape
     *
     * @return string Escaped value
     */
    public function escape($value);

    /**
     * Executes Command after when connecting to MySQL server.
     *
     * @param string $command Command to execute
     */
    public function initCommand($command);

    /**
     * Retrieves the server version.
     *
     * @return int MySQL server version
     */
    public function getVersion();

    /**
     * Begin a transaction.
     *
     * @return bool Whether the transaction was successful
     */
    public function beginTransaction();

    /**
     * Commit a transaction.
     *
     * @return bool Whether the commit was successful
     */
    public function commit();

    /**
     * Rollback a transaction.
     *
     * @return bool Whether the transaction was successful
     */
    public function rollback();
}
