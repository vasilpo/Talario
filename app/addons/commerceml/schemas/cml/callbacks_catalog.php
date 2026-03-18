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

use Tygh\Addons\CommerceML\ServiceProvider;
use Tygh\Addons\CommerceML\Xml\SimpleXmlElement;
use Tygh\Addons\CommerceML\Storages\ImportStorage;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, callable> $schema Declares parser callbacks for xml paths
 */
$schema = [
    'classifier/properties/property'   => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        ServiceProvider::getProductFeatureConvertor()->convert($xml, $import_storage);
    },
    'classifier/groups/group'          => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        return ServiceProvider::getCategoryConvertor()->convert($xml, $import_storage);
    },
    'catalog/products/product'         => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        return ServiceProvider::getProductConvetor()->convert($xml, $import_storage);
    },
    'packages/prices_types/price_type' => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        return ServiceProvider::getPriceTypeConvertor()->convert($xml, $import_storage);
    },
    'classifier/prices_types/price_type' => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        return ServiceProvider::getPriceTypeConvertor()->convert($xml, $import_storage);
    },
    'packages/offers/offer'            => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        return ServiceProvider::getProductConvetor()->convert($xml, $import_storage, false);
    },
    'catalog@has_only_changes'         => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        $import_storage->getImport()->has_only_changes = SimpleXmlElement::normalizeBool((string) $xml);
    }
];

return $schema;
