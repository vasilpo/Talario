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

class VendorOwnerFinder implements ReceiverFinderInterface
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
        $owner_id = $this->getOwnerId($message_schema);
        if (!$owner_id) {
            return [];
        }

        $conditions = [
            'users.status'  => ObjectStatuses::ACTIVE,
            'users.user_id' => $owner_id,
        ];

        return $this->db->getColumn(
            'SELECT users.email'
            . ' FROM ?:users AS users'
            . ' WHERE ?w',
            $conditions
        );
    }

    /**
     * Gets vendor owner user ID.
     *
     * @param \Tygh\Notifications\Transports\Mail\MailMessageSchema $schema Message schema
     *
     * @return int|null
     */
    protected function getOwnerId(MailMessageSchema $schema)
    {
        $company_id = null;
        if ($schema->to_company_id) {
            $company_id = $schema->to_company_id;
        }
        if (!$company_id) {
            return null;
        }

        return fn_get_company_admin_user_id($company_id);
    }
}
