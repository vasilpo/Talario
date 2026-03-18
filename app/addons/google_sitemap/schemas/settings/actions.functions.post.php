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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Warns admin about missing sitemap.
 *
 * @param string $new_value New add-on status
 * @param string $old_value Old add-on status
 *
 * @return bool
 */
function fn_settings_actions_addons_google_sitemap(&$new_value, $old_value)
{
    if ($new_value === ObjectStatuses::ACTIVE) {
        fn_set_notification(NotificationSeverity::WARNING, __('warning'), __('google_sitemap.generate_map'));
    }

    return true;
}
