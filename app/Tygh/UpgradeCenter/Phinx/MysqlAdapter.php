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


namespace Tygh\UpgradeCenter\Phinx;

use BadMethodCallException;
use Phinx\Db\Adapter\MysqlAdapter as PhinxMysqlAdapter;

/**
 * Phinx MySQL adapter.
 *
 * @since 4.4.1
 * @package Tygh\UpgradeCenter\Phinx
 */
class MysqlAdapter extends PhinxMysqlAdapter
{
    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        parent::connect();

        $this->setSessionSqlMode();
    }

    /**
     * Sets "sql_mode" variable for current session.
     *
     * @return void
     */
    protected function setSessionSqlMode()
    {
        if ($this->connection === null) {
            return;
        }

        $global_sql_mode_list = explode(',', strtoupper($this->fetchRow('SELECT @@sql_mode')[0]));
        $session_sql_mode = implode(
            ',',
            array_filter($global_sql_mode_list, static function ($mode) {
                return $mode !== 'NO_ENGINE_SUBSTITUTION';
            })
        );
        $this->execute("SET sql_mode = '{$session_sql_mode}'");
    }

    /**
     * @inheritDoc
     *
     * @throws \BadMethodCallException Exception.
     *
     * @return void
     */
    public function getQueryBuilder()
    {
        throw new BadMethodCallException('Query Builder is not supported');
    }
}
