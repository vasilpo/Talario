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
 * Class SettingTypes contains all possible setting object types.
 *
 * @package Tygh\Enum
 */
class SettingTypes
{
    const PASSWORD = 'P';
    const CHECKBOX = 'C';
    const RADIOGROUP = 'R';
    const SELECTBOX = 'S';
    const SELECTBOX_WITH_SOURCE = 'K';
    const MULTIPLE_SELECT = 'M';
    const MULTIPLE_CHECKBOXES = 'N';
    const MULTIPLE_CHECKBOXES_FOR_SELECTBOX = 'G';
    const SELECTABLE_BOX = 'B';
    const COUNTRY = 'X';
    const STATE = 'W';
    const TEMPLATE = 'E';
    const PERMANENT_TEMPLATE = 'Z';
    const HEADER = 'H';
    const INFO = 'O';
    const FILE = 'F';
    const TEXTAREA = 'T';
    const INPUT = 'I';
    const NUMBER = 'U';
    const HIDDEN = 'D';
    const PHONE = 'L';
    const UNEDITABLE = 'A';
    const FLOAT = 'Q';
}
