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

use Tygh\Addons\ProductVariations\ServiceProvider;
use Tygh\Addons\ProductVariations\Product\Group\Repository as GroupRepository;
use Tygh\Addons\ProductVariations\Product\Repository as ProductRepository;
use Tygh\Enum\YesNo;
use Tygh\Registry;

/** @var array $schema */

$schema['variations'] = function () {
    $query = ServiceProvider::getQueryFactory()->createQuery(
        GroupRepository::TABLE_GROUPS,
        [],
        ['g.id'],
        'g'
    );

    $query->addCondition('code NOT IN (?a)', [['PV-27186628F']]);
    $query->addInnerJoin('gp', GroupRepository::TABLE_GROUP_PRODUCTS, ['id' => 'group_id']);
    $query->addInnerJoin('p', ProductRepository::TABLE_PRODUCTS, ['gp.product_id' => 'product_id']);
    $query->addConditions(['status' => ['A']], 'p');
    $query->setLimit(1);

    return (bool) $query->scalar();
};

$schema['product_variations_qty_discount'] = YesNo::toBool(Registry::get('addons.product_variations.quantity_discount_on_different_variations'));

return $schema;