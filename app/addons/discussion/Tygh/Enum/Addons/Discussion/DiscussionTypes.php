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

namespace Tygh\Enum\Addons\Discussion;

/**
 * DiscussionTypes contains available discussion types for products, categories, etc.
 *
 * @package Tygh\Enum
 */
class DiscussionTypes
{
    const TYPE_COMMUNICATION_AND_RATING = 'B';
    const TYPE_COMMUNICATION = 'C';
    const TYPE_RATING = 'R';
    const TYPE_DISABLED = 'D';

    public static function getAll()
    {
        return array(
            self::TYPE_COMMUNICATION_AND_RATING => __('communication_and_rating'),
            self::TYPE_COMMUNICATION            => __('communication'),
            self::TYPE_RATING                   => __('rating'),
            self::TYPE_DISABLED                 => __('disabled'),
        );
    }
}