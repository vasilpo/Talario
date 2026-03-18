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

/**
 * Filters and validates company inn.
 *
 * @param string $value     New setting value.
 * @param string $old_value Old setting value.
 */
function fn_settings_actions_addons_rus_online_cash_register_inn($value, $old_value)
{
    $value = trim($value);

    if (function_exists('ctype_digit')) {
        $result = ctype_digit($value);
    } else {
        $result = preg_match('/^[0-9]+$/', $value) ? true : false;
    }

    if (!$result) {
        fn_set_notification('E', __('error'), __('rus_online_cash_register.inn_is_invalid'));
    }
}
