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
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');
/**
 * @psalm-var array{
 *   controllers: array{
 *     modes: array<string, array{
 *       permissions: string|bool
 *     }>
 *   }
 * } $schema
 */
$schema['controllers']['rma']['modes']['returns']['permissions'] = true;
$schema['controllers']['rma']['modes']['details']['permissions'] = true;
$schema['controllers']['rma']['modes']['print_slip']['permissions'] = true;

// FIXME: Workaround for correct privilege detection in Vendor privileges add-on
if (Registry::ifGet('addons.vendor_privileges.status', ObjectStatuses::DISABLED) === ObjectStatuses::ACTIVE) {
    $schema['controllers']['rma']['modes']['update_details']['permissions'] =
    $schema['controllers']['rma']['modes']['accept_products']['permissions'] =
    $schema['controllers']['rma']['modes']['decline_products']['permissions'] =
    $schema['controllers']['rma']['modes']['confirmation']['permissions'] =
        'manage_rma';
}

return $schema;
