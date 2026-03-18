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

/** @var array $schema */
$schema['shippings.manage']['store_locator'] = [
    'href'     => 'store_locator.manage',
    'text'     => __('store_locator.actions.store_locator'),
    'position' => 500
];

$schema['store_locator.manage']['export'] = [
    'href'     => 'exim.export?section=pickup',
    'text'     => __('actions.export'),
    'position' => 200
];

$schema['store_locator.manage']['import'] = [
    'href'     => 'exim.import?section=pickup',
    'text'     => __('actions.import'),
    'position' => 100
];

return $schema;
