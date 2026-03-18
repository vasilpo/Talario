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

namespace Tygh\Enum\Addons\AdvancedImport;

/**
 * The class declares available parsing process statuses.
 *
 * @package Tygh\Enum\Addons\AdvancedImport
 */
class ModifierParsingState
{
    const STARTING_PARSING_MODIFIER = 1;
    const STARTING_PARSING_PARAMETER = 3;

    const EXPECTING_OPENING_BRACKET = 5;
    const EXPECTING_PARAMETER_WRAPPER = 7;
    const EXPECTING_PARAMETER_DELIMITER = 9;

    const PARAMETER_PARSING_FINISHED = 11;
    const PARSING_FINISHED = 13;
}
