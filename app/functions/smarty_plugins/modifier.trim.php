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

/**
 * Smarty plugin
 *
 * @package Smarty
 *
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier<br>
 * Name:     trim<br>
 * Purpose:  Strip whitespace (or other characters) from the beginning and end of a string
 * Example:  {$string|trim}
 * -------------------------------------------------------------
 */
/**
 * Strip whitespace (or other characters) from the beginning and end of a string
 *
 * @param string $string     The string that will be trimmed
 * @param string $characters Optional characters to be stripped
 *
 * @return string
 */
function smarty_modifier_trim($string, $characters = " \n\r\t\v\x00")
{
    return trim($string ?? '', $characters);
}
