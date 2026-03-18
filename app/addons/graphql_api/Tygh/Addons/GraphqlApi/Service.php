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

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\GraphqlApi\Type\BooleanType;
use Tygh\Addons\GraphqlApi\Type\FloatType;
use Tygh\Addons\GraphqlApi\Type\IntType;
use Tygh\Addons\GraphqlApi\Type\StringType;
use Tygh\Addons\GraphqlApi\Validator\OwnershipValidator;
use Tygh\Addons\GraphqlApi\Validator\PrivilegeValidator;

class Service implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['graphql_api'] = function (Container $app) {
            Type::overrideStandardTypes([
                Type::BOOLEAN => new BooleanType(),
                Type::FLOAT   => new FloatType(),
                Type::INT     => new IntType(),
                Type::STRING  => new StringType(),
            ]);

            return new Api(
                fn_get_schema('graphql_types', 'query'),
                fn_get_schema('graphql_types', 'mutation')
            );
        };

        $app['graphql_api.validator.ownership'] = function (Container $app) {
            return new OwnershipValidator($app['db']);
        };

        $app['graphql_api.validator.privilege'] = function (Container $app) {
            return new PrivilegeValidator;
        };
    }
}
