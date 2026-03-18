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

use Tygh\ContextMenu\Items\DividerItem;

defined('BOOTSTRAP') or die('Access denied!');

/** @var array $schema */
$schema['items']['actions']['items']['actions_divider3'] = [
    'type'     => DividerItem::class,
    'position' => 60,
];

$schema['items']['actions']['items']['add_selected_to_unisender'] = [
    'name'     => ['template' => 'addons.rus_unisender.add_selected_to_unisender'],
    'dispatch' => 'unisender.add_selected',
    'data'     => [
        'action_class' => 'cm-confirm',
    ],
    'position' => 70,
];

return $schema;
