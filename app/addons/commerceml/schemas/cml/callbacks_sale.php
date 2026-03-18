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
    'document' => static function (SimpleXmlElement $xml, ImportStorage $import_storage) {
        ServiceProvider::getOrderConvertor()->convert($xml, $import_storage);
    }
];

return $schema;
