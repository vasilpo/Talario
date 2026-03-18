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

namespace Tygh\NotificationsCenter;

/**
 * Interface IFactory describes class that creates Notification builders.
 *
 * @package Tygh\NotificationsCenter
 */
interface IFactory
{
    /**
     * Creates on-site notification from its data.
     *
     * @param array $data
     *
     * @return \Tygh\NotificationsCenter\Notification
     */
    public function fromArray(array $data);

    /**
     * Gets builder to create on-site notifications.
     *
     * @param $type
     *
     * @return \Tygh\NotificationsCenter\NotificationBuilders\INotificationBuilder
     */
    public function getNotificationBuilder($type);
}
