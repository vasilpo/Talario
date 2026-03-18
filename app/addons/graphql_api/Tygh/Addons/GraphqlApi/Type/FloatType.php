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
use GraphQL\Type\Definition\FloatType as BaseType;

class FloatType extends BaseType
{
    /** @inheritDoc */
    public function parseValue($value): float
    {
        return parent::parseValue((float) $value);
    }

    /** @inheritDoc */
    public function parseLiteral($value_node, ?array $variables = null)
    {
        if (
            $value_node instanceof FloatValueNode
            || $value_node instanceof StringValueNode
            || $value_node instanceof IntValueNode
        ) {
            return (float) $value_node->value;
        }

        return parent::parseLiteral($value_node, $variables);
    }
}
