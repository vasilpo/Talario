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

class ProfileFieldSections
{
    const ESSENTIALS = 'E';
    const CONTACT_INFORMATION = 'C';
    const BILLING_ADDRESS = 'B';
    const SHIPPING_ADDRESS = 'S';

    const STATUS_ACTIVE = 'A';
    const STATUS_DEPRECATED = 'R';

    // actually, is not a section, but is used on the profile field update page
    const BILLING_AND_SHIPPING_ADDRESS = 'BS';

    public static function getAll($lang_code = CART_LANGUAGE)
    {
        return [
            self::ESSENTIALS                   => '',
            self::CONTACT_INFORMATION          => __('contact_information', [], $lang_code),
            self::BILLING_ADDRESS              => __('billing_address', [], $lang_code),
            self::SHIPPING_ADDRESS             => __('shipping_address', [], $lang_code),
            self::BILLING_AND_SHIPPING_ADDRESS =>
                __('billing_address', [], $lang_code)
                . '/'
                . __('shipping_address', [], $lang_code),
        ];
    }
}