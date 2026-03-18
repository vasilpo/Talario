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

namespace Tygh\Addons\GraphqlApi\Validator;

use Tygh\Database\Connection;

class OwnershipValidator
{
    /**
     * @var \Tygh\Database\Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function validateProduct(int $product_id, int $company_id): bool
    {
        if ($company_id === 0) {
            return true;
        }

        $product_company_id = (int) $this->db->getField(
            'SELECT company_id FROM ?:products WHERE product_id = ?i',
            $product_id
        );

        return $product_company_id === $company_id;
    }

    public function validateOrder(int $order_id, int $company_id): bool
    {
        if ($company_id === 0) {
            return true;
        }

        $order_company_id = (int) $this->db->getField(
            'SELECT company_id FROM ?:orders WHERE order_id = ?i',
            $order_id
        );

        return $order_company_id === $company_id;
    }
}
