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


use Tygh\Addons\ProductVariations\Product\Sync\Table\OneToManyViaPrimaryKeyTable;

/** @var array $schema */

$schema['reward_points'] = OneToManyViaPrimaryKeyTable::create('reward_points', ['object_id', 'object_type', 'usergroup_id', 'company_id'], 'object_id', ['reward_point_id'], ['conditions' => ['object_type' => 'P']]);
$schema['product_point_prices'] = OneToManyViaPrimaryKeyTable::create('product_point_prices', ['product_id', 'lower_limit', 'usergroup_id'], 'product_id', ['point_price_id']);

return $schema;