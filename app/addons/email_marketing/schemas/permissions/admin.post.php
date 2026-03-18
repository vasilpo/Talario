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

$schema['em_subscribers'] = array (
    'modes' => array (
        'delete' => array (
            'permissions' => 'manage_email_marketing'
        )
    ),
    'permissions' => array ('GET' => 'view_email_marketing', 'POST' => 'manage_email_marketing')
);

$schema['tools']['modes']['update_status']['param_permissions']['table']['em_subscribers'] = 'manage_email_marketing';

$schema['exim']['modes']['export']['param_permissions']['section']['subscribers'] = 'view_email_marketing';
$schema['exim']['modes']['import']['param_permissions']['section']['subscribers'] = 'manage_email_marketing';

return $schema;
