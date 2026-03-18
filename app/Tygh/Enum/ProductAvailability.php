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

class ProductAvailability
{
    const OUT_OF_STOCK = 'out of stock';
    const IN_STOCK     = 'in stock';
    const PRE_ORDER    = 'on backorder';

    const KEY_OUT_OF_STOCK = 'OUT_OF_STOCK';
    const KEY_IN_STOCK     = 'IN_STOCK';
    const KEY_PRE_ORDER    = 'PRE_ORDER';

    /**
     * @return array<string>
     */
    public static function getAll()
    {
        return [
            self::KEY_OUT_OF_STOCK => self::OUT_OF_STOCK,
            self::KEY_IN_STOCK     => self::IN_STOCK,
            self::KEY_PRE_ORDER    => self::PRE_ORDER
        ];
    }
}
