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

namespace Tygh\Addons\AdvancedImport\Modifiers\Parsers;

use Tygh\Addons\AdvancedImport\Exceptions\InvalidModifierFormatException;
use Tygh\Addons\AdvancedImport\Exceptions\InvalidModifierParameterException;

/**
 * The interface of the parser class responsible for parsing modifiers.
 *
 * @package Tygh\Addons\AdvancedImport\Modifiers\Parsers
 */
interface IModifierParser
{
    /**
     * Parses modifier string that contains the operation
     *
     * @param string $modifier The modifier operator
     *
     * @return mixed
     * @throws InvalidModifierFormatException
     * @throws InvalidModifierParameterException
     */
    public function parse($modifier);
}
