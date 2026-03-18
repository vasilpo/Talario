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

namespace Tygh\Notifications\Transports\Internal\ReceiverFinders;

use Tygh\Database\Connection;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\Transports\Internal\InternalMessageSchema;

/**
 * Class VendorOwnerFinder finds main administrators of vendors.
 *
 * @package Tygh\Notifications\Transports\Internal\ReceiverFinders
 */
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
    public function find($criterion, InternalMessageSchema $message_schema)
    {
        $owner_id = $this->getOwnerId($message_schema);
        if (!$owner_id) {
            return [];
        }

        $conditions = [
            'users.status'  => ObjectStatuses::ACTIVE,
            'users.user_id' => $owner_id,
        ];

        return $this->db->getSingleHash(
            'SELECT users.user_id AS user_id, (CASE WHEN users.user_type = ?s THEN ?s ELSE ?s END) AS area'
            . ' FROM ?:users AS users'
            . ' WHERE ?w',
            ['user_id', 'area'],
            UserTypes::CUSTOMER,
            UserTypes::CUSTOMER,
            UserTypes::ADMIN,
            $conditions
        );
    }

    /**
     * Finds user id for owner of company that should receive internal message.
     *
     * @param InternalMessageSchema $schema Schema of internal message.
     *
     * @return int|null
     */
    protected function getOwnerId(InternalMessageSchema $schema)
    {
        $company_id = null;
        if ($schema->to_company_id) {
            $company_id = (int) $schema->to_company_id;
        }
        if (!$company_id) {
            return null;
        }

        return fn_get_company_admin_user_id($company_id);
    }
}
