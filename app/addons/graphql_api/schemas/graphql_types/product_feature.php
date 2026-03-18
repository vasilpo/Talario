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

defined('BOOTSTRAP') or die('Access denied');

use GraphQL\Deferred;
use Tygh\Addons\GraphqlApi\Context;
use Tygh\Addons\GraphqlApi\Type;
use Tygh\Registry;

$schema = [
    'name'        => 'ProductFeature',
    'description' => 'Represents product feature',
    'fields'      => [
        'feature_id'   => [
            'type'        => Type::int(),
            'description' => 'Feature ID',
        ],
        'value'        => [
            'type'        => Type::string(),
            'description' => 'Feature value (string features only)',
        ],
        'variant_id'   => [
            'type'        => Type::int(),
            'description' => 'Selected feature variant (selectable features only)',
        ],
        'variant'      => [
            'type'        => Type::string(),
            'description' => 'Selected feature variant text (selectable features only)',
        ],
        'feature_type' => [
            'type'        => Type::string(),
            'description' => 'Type',
        ],
        'description'  => [
            'type'        => Type::string(),
            'description' => 'Name',
        ],
        'parent_id'    => [
            'type'        => Type::int(),
            'description' => 'Feature group ID',
        ],
        'value_int'    => [
            'type'        => Type::float(),
            'description' => 'Feature value (UNIX timestamp date and numeric features)',
        ],
        'variants'     => [
            'type'    => Type::listOf(Type::resolveType('product_feature_variant')),
            'args'    => [
                'page'           => [
                    'type'         => Type::int(),
                    'defaultValue' => 1,
                    'description'  => 'Page',
                ],
                'items_per_page' => [
                    'type'         => Type::int(),
                    'defaultValue' => Registry::get('settings.Appearance.admin_elements_per_page'),
                    'description'  => 'Items per page',
                ],
            ],
            'resolve' => static function ($source, $args, Context $context) {
                list($variants,) = fn_get_product_feature_variants(
                    [
                        'feature_id'     => $source['feature_id'],
                        'page_id'        => $args['page'],
                        'items_per_page' => $args['items_per_page'],
                        'product_id'     => Registry::get('runtime.api.product_id')
                    ],
                    $args['items_per_page'],
                    $context->getLanguageCode()
                );

                return $variants;
            }
        ],
    ],
];

return $schema;
