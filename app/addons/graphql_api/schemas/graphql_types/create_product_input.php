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

use Tygh\Addons\GraphqlApi\InputType as Type;

$schema = fn_get_schema('graphql_types', 'update_product_input');

$schema['name'] = 'CreateProductInput';
$schema['description'] = 'Represents a set of data required to create a product';

$schema['fields']['category_ids']['type'] = Type::nonNull($schema['fields']['category_ids']['type']);

$schema['fields']['product']['type'] = Type::nonNull($schema['fields']['product']['type']);

$schema['fields']['price']['type'] = Type::nonNull($schema['fields']['price']['type']);

return $schema;
