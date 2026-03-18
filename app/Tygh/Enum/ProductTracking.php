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
 * ProductTracking contains possible values for `products`.`tracking` DB field.
 *
 * @package Tygh\Enum
 */
class ProductTracking
{
    /**
     * Track product amount
     */
    const TRACK = 'B';

    /**
     * Backward compatibility
     */
    const TRACK_WITH_OPTIONS = self::TRACK;

    /**
     * Backward compatibility
     */
    const TRACK_WITHOUT_OPTIONS = self::TRACK;

    /**
     * Do not track product amount
     */
    const DO_NOT_TRACK = 'D';
}
