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

namespace Tygh\Addons\GraphqlApi;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as BaseType;

class Type extends ObjectType
{
    protected static $types;

    public static function resolveType($type)
    {
        if ($type instanceof BaseType) {
            return $type;
        }

        if (isset(static::$types[$type])) {
            return static::$types[$type];
        }

        if (class_exists($type)) {
            return static::$types[$type] = new $type;
        }

        return static::$types[$type] = new Type(static::getTypeConfig($type));
    }

    /**
     * @param \GraphQL\Type\Definition\Type|string $wrapped_type Wrapped type
     *
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public static function listOf($wrapped_type): ListOfType
    {
        return new ListOfType(static::resolveType($wrapped_type));
    }

    /**
     * @param \GraphQL\Type\Definition\Type|string $wrapped_type Wrapped type
     *
     * @return \GraphQL\Type\Definition\NonNull
     */
    public static function nonNull($wrapped_type): NonNull
    {
        return new NonNull(static::resolveType($wrapped_type));
    }

    protected static function getTypeConfig($type)
    {
        $config = fn_get_schema('graphql_types', $type);

        if (!$config) {
            //TODO throw not found exception
        }

        return $config;
    }
}
