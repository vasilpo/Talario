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

namespace Tygh\Enum;

/**
 * Class ReceiverSearchMethods contains possible search methods for Notifications center.
 *
 * @package Tygh\Enum
 */
class ReceiverSearchMethods
{
    const USER_ID = 'user_id';
    const USERGROUP_ID = 'usergroup_id';
    const EMAIL = 'email';
    const ORDER_MANAGER = 'order_manager';
    const VENDOR_OWNER = 'vendor_owner';
    const VENDOR_EMAIL = 'vendor_email';
}
