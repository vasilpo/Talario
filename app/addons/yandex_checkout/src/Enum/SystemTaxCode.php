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

namespace Tygh\Addons\YandexCheckout\Enum;

/**
 * Class SystemTaxCode contains possible values for the `system_tax_code` API request field.
 *
 * @package Tygh\Addons\YandexCheckout\Enum
 */
class SystemTaxCode
{
    public const OSN = 1;
    public const USN_INCOME = 2;
    public const USN_INCOME_OUTCOME = 3;
    public const ENVD = 4;
    public const ESN = 5;
    public const PATENT = 6;

    /**
     * Returns all tax systems with descriptions.
     *
     * @return array<int, string>
     */
    public static function getAll(): array
    {
        return [
            self::OSN                => __('rus_taxes.tax_system.osn'),
            self::USN_INCOME         => __('rus_taxes.tax_system.usn_income'),
            self::USN_INCOME_OUTCOME => __('rus_taxes.tax_system.usn_income_outcome'),
            self::ENVD               => __('rus_taxes.tax_system.envd'),
            self::ESN                => __('rus_taxes.tax_system.esn'),
            self::PATENT             => __('rus_taxes.tax_system.patent'),
        ];
    }
}
