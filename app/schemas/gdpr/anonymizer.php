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

// key is field in the data base, for value see \Tygh\Gdpr\DataModifier\UserPersonalDataAnonymizer::modifyValue
$schema = array(
    'email'          => '%rand%@example.com',
    'firstname'      => '*',
    'lastname'       => '*',
    'b_firstname'    => '*',
    'b_lastname'     => '*',
    'b_address'      => '*',
    'b_address_2'    => '*',
    'b_city'         => '*',
    'b_country'      => '*',
    'b_state'        => '*',
    'b_county'       => '',
    'b_zipcode'      => '*',
    'b_phone'        => '0000000000',
    's_firstname'    => '*',
    's_lastname'     => '*',
    's_address'      => '*',
    's_address_2'    => '*',
    's_city'         => '*',
    's_country'      => '*',
    's_state'        => '*',
    's_county'       => '',
    's_zipcode'      => '*',
    's_phone'        => '0000000000',
    's_address_type' => '',
    'phone'          => '0000000000',
    'fax'            => '',
    'url'            => '*',
    'ip_address'     => '1.2.3.4',
    'b_state_descr'  => '',
    's_state_descr'  => '',
    'address'        => '*',
    'address_2'      => '*',
    'city'           => '*',
    'country'        => '*',
    'state'          => '*',
    'county'         => '',
    'zipcode'        => '*',
    'country_descr'  => '',
    'state_descr'    => '',
    'birthday'       => '',
    'user_login'     => '*',
    'name'           => '*',
);

return $schema;
