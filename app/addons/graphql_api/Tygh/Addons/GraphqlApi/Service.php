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
