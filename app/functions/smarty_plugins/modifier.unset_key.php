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
 * -------------------------------------------------------------
 * Type:     modifier<br>
 * Name:     unset_key<br>
 * Purpose:  destroys the specified array variable by key
 * Example:  {$a|unset_key:$b}
 * -------------------------------------------------------------
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 *
 * @param mixed[]    $array The array to work on
 * @param string|int $key   The variable to be delete
 *
 * @return mixed[]
 *
 * @package Smarty
 *
 * @subpackage plugins
 */
function smarty_modifier_unset_key(array $array, $key)
{
    unset($array[$key]);
    return $array;
}
