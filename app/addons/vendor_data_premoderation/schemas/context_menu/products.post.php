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

use Tygh\ContextMenu\Items\ComponentItem;

defined('BOOTSTRAP') or die('Access denied!');

/** @var array $schema */
$schema['items']['vendor_data_premoderation.product_approval'] = [
    'name'                => ['template' => 'product_approval'],
    'type'                => ComponentItem::class,
    'template'            => 'addons/vendor_data_premoderation/components/context_menu/products/product_approval.tpl',
    'permission_callback' => static function () {
        return fn_check_permissions('premoderation', 'm_approve', 'admin');
    },
    'position'            => 70,
];

return $schema;
