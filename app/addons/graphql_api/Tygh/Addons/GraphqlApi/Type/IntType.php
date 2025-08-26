<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
