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

namespace Tygh\Licensing;

use Tygh\Licensing\Rules\CountRule;

class FeatureRulesMap
{
    /**
     * @param string $feature Value of Feature enum
     * @param string $rule    Rule class
     *
     * @return \Closure|null
     */
    public static function getHandler($feature, $rule)
    {
        $map = [
            Features::ADD_STOREFRONT => [
                CountRule::class => static function () {
                    return db_get_field('SELECT COUNT(*) FROM ?:storefronts');
                }
            ]
        ];

        return $map[$feature][$rule] ?? null;
    }
}
