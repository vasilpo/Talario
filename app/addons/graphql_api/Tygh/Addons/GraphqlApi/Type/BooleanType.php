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

use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\BooleanType as BaseType;
use Tygh\Enum\YesNo;

class BooleanType extends BaseType
{
    /**
     * @param string $value Y/N
     */
    public function serialize($value): bool
    {
        return YesNo::toBool($value);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof BooleanValueNode || $valueNode instanceof StringValueNode) {
            return YesNo::toBool($valueNode->value);
        }

        return parent::parseLiteral($valueNode, $variables);
    }
}
