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

use Tygh\Registry;

if (!Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {

    /** @var array $schema */
    $schema['em_subscribers.manage']['export'] = [
        'href'     => 'exim.export?section=subscribers',
        'text'     => __('actions.export'),
        'position' => 201
    ];

    $schema['em_subscribers.manage']['import'] = [
        'href'     => 'exim.import?section=subscribers',
        'text'     => __('actions.import'),
        'position' => 101
    ];
}

return $schema;
