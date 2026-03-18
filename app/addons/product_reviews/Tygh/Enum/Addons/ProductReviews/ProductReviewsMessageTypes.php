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

namespace Tygh\Enum\Addons\ProductReviews;

/**
 * Class MessageTypes
 *
 * @package Tygh\Enum\Addons\ProductReviews
 */
class ProductReviewsMessageTypes
{
    const ADVANTAGES    = 'advantages';
    const DISADVANTAGES = 'disadvantages';
    const COMMENT       = 'comment';

    /**
     * Gets types by mode
     *
     * @param string|null $mode Mode (see Registry::get('addons.product_reviews.review_fields'))
     *
     * @return string[]
     */
    public static function getTypes($mode = null)
    {
        if ($mode === null || $mode === 'advanced') {
            return [self::ADVANTAGES, self::DISADVANTAGES, self::COMMENT];
        }

        return [self::COMMENT];
    }
}
