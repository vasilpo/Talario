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


use Tygh\Addons\ProductVariations\Product\Sync\Table\OneToManyViaFieldTable;
use Tygh\Addons\ProductVariations\Product\Sync\Table\OneToManyViaRelationTable;

/** @var array $schema */

$schema['buy_together'] = OneToManyViaFieldTable::create('buy_together', ['chain_id'], 'product_id');
$schema['buy_together_descriptions'] = OneToManyViaRelationTable::create('buy_together_descriptions', ['chain_id', 'lang_code'], 'buy_together', ['chain_id' => 'chain_id'], 'product_id');

return $schema;