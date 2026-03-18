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
 *  ProfileFieldTypes contains possible values for `profile_fields`.`field_type` DB field.
 *
 * @package Tygh\Enum
 */
class ProfileFieldTypes
{
    const ADDRESS_TYPE = 'N';
    const CHECKBOX = 'C';
    const COUNTRY = 'O';
    const DATE = 'D';
    const EMAIL = 'E';
    const HEADER = 'H';
    const INPUT = 'I';
    const PASSWORD = 'W';
    const PHONE = 'P';
    const POSTAL_CODE = 'Z';
    const RADIO = 'R';
    const SELECT_BOX = 'S';
    const STATE = 'A';
    const TEXT_AREA = 'T';
    const USER_GROUP = 'U';
    const VENDOR_TERMS = 'B';
    const FILE = 'F';
}
