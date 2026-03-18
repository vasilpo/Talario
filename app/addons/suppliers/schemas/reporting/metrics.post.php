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

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SuppliersObjectTypes;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['suppliers_is_used'] = static function () {
    return (bool) db_get_field(
        'SELECT COUNT(1) FROM ?:suppliers as suppliers'
        . ' LEFT JOIN ?:supplier_links as links on links.supplier_id = suppliers.supplier_id'
        . ' LEFT JOIN ?:products as products on products.product_id = links.object_id'
        . ' WHERE links.object_type = ?s AND suppliers.status = ?s AND products.status = ?s',
        SuppliersObjectTypes::PRODUCT,
        ObjectStatuses::ACTIVE,
        ObjectStatuses::ACTIVE
    );
};

return $schema;
