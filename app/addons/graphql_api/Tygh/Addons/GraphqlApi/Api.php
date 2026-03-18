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

use GraphQL\Error\Error;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;

class Api
{
    /**
     * @var \GraphQL\Type\Schema
     */
    protected $schema;

    public function __construct(array $query_schema, array $mutation_schema)
    {
        $this->schema = new Schema([
            'query'    => new ObjectType($query_schema),
            'mutation' => new ObjectType($mutation_schema),
        ]);
    }

    /**
     * Executes GraphQL query.
     *
     * @param string|\GraphQL\Language\AST\DocumentNode $query     Query
     * @param array                                     $variables Query variables
     * @param \Tygh\Addons\GraphqlApi\Context           $context   Query context
     *
     * @return \GraphQL\Executor\ExecutionResult
     */
    public function execute($query, array $variables, Context $context): ExecutionResult
    {
        return GraphQL::executeQuery(
            $this->schema,
            $query,
            null,
            $context,
            $variables
        );
    }

    /**
     * Dispatches GraphQL request to the corresponding entity and returns its response.
     *
     * @param mixed                                $source
     * @param array                                $args
     * @param \Tygh\Addons\GraphqlApi\Context      $context
     * @param \GraphQL\Type\Definition\ResolveInfo $info
     *
     * @return mixed
     * @throws \GraphQL\Error\Error
     */
    public static function dispatchRequest($source, $args, Context $context, ResolveInfo $info)
    {
        $operation_type = fn_camelize($info->operation->operation);
        $field = fn_camelize($info->fieldName);

        $operation_class = sprintf('\Tygh\Addons\GraphqlApi\Operation\%s\%s', $operation_type, $field);
        if (!class_exists($operation_class)) {
            throw new Error('Not found');
        }
        /** @var \Tygh\Addons\GraphqlApi\Operation\OperationInterface $operation */
        $operation = new $operation_class($source, $args, $context);

        /** @var \Tygh\Addons\GraphqlApi\Validator\PrivilegeValidator $privilege_validator */
        $privilege_validator = $context->getApp()['graphql_api.validator.privilege'];
        if (!$privilege_validator->validate($context->getUserId(), $context->getUserType(), $operation)) {
            throw new Error('Access denied');
        }

        $result = $operation->run();

        return $result;
    }
}
