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

namespace Tygh\Enum\Addons\VendorCommunication;

/**
 * Class CommunicationTypes
 * Describes types of communications
 *
 * @package Tygh\Addons\VendorCommunication\Enum
 */
class CommunicationTypes
{
    const VENDOR_TO_CUSTOMER = 'vendor_to_customer';
    const VENDOR_TO_ADMIN = 'vendor_to_admin';

    /**
     * @return array
     */
    public static function all()
    {
        $types = [];
        $types[] = self::VENDOR_TO_CUSTOMER;
        if (fn_allowed_for('MULTIVENDOR')) {
            $types[] = self::VENDOR_TO_ADMIN;
        }
        return $types;
    }
}
