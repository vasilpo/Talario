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

class VendorPayoutApprovalStatuses
{
    const COMPLETED = 'C';
    const PENDING = 'P';
    const DECLINED = 'D';

    public static function getAll()
    {
        return array(
            self::PENDING => self::PENDING,
            self::COMPLETED => self::COMPLETED,
            self::DECLINED => self::DECLINED,
        );
    }

    public static function getWithDescriptions($lang_code = CART_LANGUAGE)
    {
        static $statuses;
        if (!$statuses) {
            $statuses = array();
            foreach (self::getAll() as $status) {
                $statuses[$status] = __("vendor_payouts.approval_status.{$status}", array(), $lang_code);
            }
        }

        return $statuses;
    }
}
