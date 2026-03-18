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
 * @return string Notification text displayed at the add-on settings.
 */
function fn_recaptcha_settings_notice_handler()
{
    return __('recaptcha.text_settings_notice');
}

/**
 * @return string Notification text displayed at the add-on settings.
 */
function fn_recaptcha_forbidden_countries_notice_handler()
{
    return __('recaptcha.text_forbidden_countries_notice');
}

/**
 * Provides variants for forbidden countries setting
 *
 * @return array
 */
function fn_settings_variants_addons_recaptcha_forbidden_countries()
{
    return fn_get_simple_countries();
}

