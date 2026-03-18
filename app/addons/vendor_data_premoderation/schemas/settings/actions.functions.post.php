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
 * Shows warning notification after add-on status changed
 *
 * @param string $new_value New values of vendor_data_premoderation setting
 * @param string $old_value Old values of vendor_data_premoderation setting
 */
function fn_settings_actions_addons_vendor_data_premoderation($new_value, $old_value)
{
    if ($new_value == 'D') {
        fn_vendor_data_premoderation_display_notification_for_deleted_statuses();
    }
}