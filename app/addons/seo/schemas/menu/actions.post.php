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
$schema['seo_rules.manage']['seo.redirects_manager'] = [
    'href'     => 'seo_redirects.manage',
    'text'     => __('seo.actions.redirects_manager'),
    'position' => 100
];

$schema['seo_rules.manage']['seo_robots'] = [
    'href'     => 'robots.manage',
    'text'     => __('actions.seo_robots'),
    'position' => 200
];

$schema['seo_rules.manage']['seo_llms'] = [
    'href'     => 'llms.manage',
    'text'     => __('actions.seo_llms'),
    'position' => 300
];

return $schema;
