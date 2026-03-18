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

$schema['gift_certificates.add'] = array (
    array (
        'title' => 'gift_certificates',
        'link' => 'gift_certificates.manage'
    )
);

$schema['gift_certificates.update'] = array (
    array (
        'type' => 'search',
        'prev_dispatch' => 'gift_certificates.manage',
        'title' => 'search_results',
        'link' => 'gift_certificates.manage.last_view'
    ),
    array (
        'title' => 'gift_certificates',
        'link' => 'gift_certificates.manage.reset_view'
    )
);

return $schema;
