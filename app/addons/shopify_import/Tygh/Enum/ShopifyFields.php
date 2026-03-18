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

namespace Tygh\Enum;

/**
 * Class ShopifyFields contains default Shopify fields from CSV-file.
 *
 * @package Tygh\Enum
 */
class ShopifyFields
{
    const HANDLE = 'Handle';
    const IMAGE_SRC = 'Image Src';
    const IMAGE_POSITION = 'Image Position';
    const STATUS = 'Status';
    const VARIANT_SKU = 'Variant SKU';
    const VARIANT_INVENTORY_POLICY = 'Variant Inventory Policy';
    const VARIANT_FULFILLMENT_SERVICE = 'Variant Fulfillment Service';
    const VARIANT_IMAGE = 'Variant Image';
    const TITLE = 'Title';
}
