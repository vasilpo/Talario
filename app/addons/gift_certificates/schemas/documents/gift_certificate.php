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

$schema = array(
    'gift_certificate' => array(
        'class' => '\Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables\GiftCertificate',
        'arguments' => array('#context', '#config', '@formatter'),
    ),
    'company' => array(
        'class' => '\Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables\CompanyVariable',
        'alias' => 'c',
        'email_separator' => '<br/>'
    ),
    'settings' => array(
        'class' => '\Tygh\Template\Document\Variables\SettingsVariable',
    ),
    'runtime' => array(
        'class' => '\Tygh\Template\Document\Variables\RuntimeVariable'
    )
);

return $schema;