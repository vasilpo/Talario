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
 * Clears invalid values entered and sets the default return period
 *
 * @param string $new_value New return period
 * @param string $old_value Old return period
 *
 * @return void
 *
 * @param-out string|int $new_value
 */
function fn_settings_actions_addons_rma_return_period(&$new_value, $old_value)
{
    if (is_numeric($new_value)) {
        $new_value = (int) $new_value;
    } else {
        $new_value = $old_value;
    }
}
