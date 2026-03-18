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
$schema['statuses.manage']['statuses_' . strtolower(STATUSES_GIFT_CERTIFICATE)] = [
    'href'     => 'statuses.manage?type=' . STATUSES_GIFT_CERTIFICATE,
    'text'     => __('gift_certificates.actions.gift_certificate_statuses'),
    'position' => 300
];

$schema['gift_certificates.manage']['statuses_' . strtolower(STATUSES_GIFT_CERTIFICATE)] = [
    'href'     => 'statuses.manage?type=' . STATUSES_GIFT_CERTIFICATE,
    'text'     => __('gift_certificates.actions.gift_certificate_statuses'),
    'position' => 100
];

return $schema;
