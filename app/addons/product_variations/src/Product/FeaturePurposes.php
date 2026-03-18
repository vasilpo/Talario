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


namespace Tygh\Addons\ProductVariations\Product;

/**
 * Class FeaturePurposes
 *
 * @package Tygh\Addons\ProductVariations\Product
 */
class FeaturePurposes
{
    const CREATE_CATALOG_ITEM = 'group_catalog_item';

    const CREATE_VARIATION_OF_CATALOG_ITEM = 'group_variation_catalog_item';

    public static function isCreateCatalogItem($purpose)
    {
        return $purpose === self::CREATE_CATALOG_ITEM;
    }

    public static function isCreateVariationOfCatalogItem($purpose)
    {
        return $purpose === self::CREATE_VARIATION_OF_CATALOG_ITEM;
    }

    public static function getAll()
    {
        return [self::CREATE_CATALOG_ITEM, self::CREATE_VARIATION_OF_CATALOG_ITEM];
    }
}