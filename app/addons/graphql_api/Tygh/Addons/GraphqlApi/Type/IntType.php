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

namespace Tygh\Addons\GraphqlApi\Type;

use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\IntType as BaseType;

class IntType extends BaseType
{
    /** @inheritDoc */
    public function parseValue($value): int
    {
        return parent::parseValue((int) $value);
    }

    /** @inheritDoc */
    public function parseLiteral($value_node, ?array $variables = null)
    {
        if (
            $value_node instanceof FloatValueNode
            || $value_node instanceof StringValueNode
            || $value_node instanceof IntValueNode
        ) {
            return (int) $value_node->value;
        }

        return parent::parseLiteral($value_node, $variables);
    }
}
