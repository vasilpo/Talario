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

$schema['controllers']['import']['sections']['subscribers']['permission'] = false;
$schema['controllers']['export']['sections']['subscribers']['permission'] = false;

$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['newsletter_campaigns'] = false;
$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['mailing_lists'] = false;

$schema['controllers']['exim']['modes']['export']['param_permissions']['section']['subscribers'] = false;
$schema['controllers']['exim']['modes']['import']['param_permissions']['section']['subscribers'] = false;

return $schema;
