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

namespace Tygh\NotificationsCenter\NotificationBuilders;

/**
 * Interface INotificationBuilder describes the class responsible for building an on-site notification from the
 * parameters.
 *
 * @package Tygh\NotificationsCenter\NotificationBuilders
 */
interface INotificationBuilder
{
    /**
     * @param array  $params
     * @param string $area
     * @param string $lang_code
     *
     * @return \Tygh\NotificationsCenter\Notification
     */
    public function createNotification($params, $area, $lang_code);
}
