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

namespace Tygh\Enum;

/**
 *  ProfileTypes contains possible values for `profile_fields`.`profile_type` DB field.
 *
 * @package Tygh\Enum
 */
class ProfileTypes
{
    /** @var string Represents all current users (admins, customer, vendors) */
    const USER = 'user';
    const CODE_USER = 'U';

    /** @var string Represents seller (company in MVE edition) */
    const SELLER = 'seller';
    const CODE_SELLER = 'S';
}
