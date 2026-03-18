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
 * Class OrderManagerFinder finds order managers.
 *
 * @package Tygh\Notifications\Transports\Internal\ReceiverFinders
 */
class OrderManagerFinder implements ReceiverFinderInterface
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
        $order_id = $this->getOrderId($message_schema->data);
        if (!$order_id) {
            return [];
        }

        $conditions = [
            'users.status'    => ObjectStatuses::ACTIVE,
            'orders.order_id' => $order_id,
        ];

        return $this->db->getSingleHash(
            'SELECT users.user_id AS user_id, (CASE WHEN users.user_type = ?s THEN ?s ELSE ?s END) AS area'
            . ' FROM ?:users AS users'
            . ' INNER JOIN ?:orders AS orders ON orders.issuer_id = users.user_id'
            . ' WHERE ?w',
            ['user_id', 'area'],
            UserTypes::CUSTOMER,
            UserTypes::CUSTOMER,
            UserTypes::ADMIN,
            $conditions
        );
    }

    protected function getOrderId(array $data)
    {
        $order_id = null;
        if (isset($data['order_id'])) {
            $order_id = (int) $data['order_id'];
        } elseif (isset($data['order_info']['order_id'])) {
            $order_id = (int) $data['order_info']['order_id'];
        }

        return $order_id;
    }
}
