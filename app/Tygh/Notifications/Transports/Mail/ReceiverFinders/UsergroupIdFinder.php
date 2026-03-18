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

namespace Tygh\Notifications\Transports\Mail\ReceiverFinders;

use Tygh\Database\Connection;
use Tygh\Enum\ObjectStatuses;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;

class UsergroupIdFinder implements ReceiverFinderInterface
{
    /**
     * @var \Tygh\Database\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function find($criterion, MailMessageSchema $message_schema)
    {
        $conditions = [
            'users.status'            => ObjectStatuses::ACTIVE,
            'usergroups.status'       => ObjectStatuses::ACTIVE,
            'usergroups.usergroup_id' => (int) $criterion,
        ];

        if ($message_schema->to_company_id !== null) {
            $conditions['users.company_id'] = [0, $message_schema->to_company_id];
        }

        return $this->db->getColumn(
            'SELECT users.email'
            . ' FROM ?:users AS users'
            . ' LEFT JOIN ?:usergroup_links AS usergroups ON usergroups.user_id = users.user_id'
            . ' WHERE ?w'
            . ' GROUP BY users.user_id',
            $conditions
        );
    }
}
