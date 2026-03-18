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

namespace Tygh\Backend\Session;

/**
 * Class Database
 *
 * @package Tygh\Backend\Session
 */
class Database extends ABackend
{
    /**
     * @inheritDoc
     */
    public function read($sess_id)
    {
        $session = db_get_row('SELECT data FROM ?:sessions WHERE session_id = ?s', $sess_id);

        if (empty($session)) {
            return false;
        }

        return $session['data'];
    }

    /**
     * @inheritDoc
     */
    public function write($sess_id, $data)
    {
        $data['session_id'] = $sess_id;

        db_replace_into('sessions', $data);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function regenerate($old_id, $new_id)
    {
        db_query('UPDATE ?:sessions SET session_id = ?s WHERE session_id = ?s', $new_id, $old_id);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($sess_id)
    {
        db_query('DELETE FROM ?:sessions WHERE session_id = ?s', $sess_id);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function gc($max_lifetime)
    {
        // delete old sessions
        db_query('DELETE FROM ?:sessions WHERE expiry < ?i', TIME);

        return true;
    }

    /**
     * Gets sessions that were used less than number of seconds, defined in ttl_online property of Session class
     *
     * @param string $area Session area
     *
     * @return array<string> List of session IDs
     */
    public function getOnline($area)
    {
        return db_get_fields(
            'SELECT session_id FROM ?:sessions WHERE expiry > ?i AND SUBSTR(session_id, -1) = ?s',
            TIME + $this->config['ttl'] - $this->config['ttl_online'],
            $area
        );
    }
}
