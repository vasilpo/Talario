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

namespace Tygh\Enum\Addons\VendorDataPremoderation;

/**
 * Class PremoderationStatuses
 *
 * @deprecated since 4.11.1. Use specific approval methods and proper product statuses instead.
 *
 * @package    Tygh\Enum\Addons\VendorDataPremoderation
 *
 * @see        fn_vendor_data_premoderation_approve_products
 * @see        fn_vendor_data_premoderation_disapprove_products
 * @see        fn_vendor_data_premoderation_request_approval_for_products
 * @see        \Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses
 */
class PremoderationStatuses
{
    const APPROVED = 'Y';
    const DISAPPROVED = 'N';
    const PENDING = 'P';
}
