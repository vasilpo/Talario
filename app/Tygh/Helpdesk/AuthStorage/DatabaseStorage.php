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

namespace Tygh\Helpdesk\AuthStorage;

use Tygh\Database\Connection;

class DatabaseStorage implements StorageInterface
{
    /**
     * @var \Tygh\Database\Connection
     */
    private $db;

    /**
     * @param \Tygh\Database\Connection $db Database connection instance
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /** @inheritdoc */
    public function setId($user_id, $external_user_id)
    {
        $this->db->query('UPDATE ?:users SET helpdesk_user_id = ?i WHERE user_id = ?i', $external_user_id, $user_id);
    }

    /** @inheritdoc */
    public function resetId($user_id)
    {
        $this->setId($user_id, 0);
    }

    /** @inheritdoc */
    public function getId($user_id)
    {
        $id = $this->db->query('SELECT helpdesk_user_id FROM ?:users WHERE user_id = ?i', $user_id);

        return $id
            ? (int) $id
            : null;
    }
}
