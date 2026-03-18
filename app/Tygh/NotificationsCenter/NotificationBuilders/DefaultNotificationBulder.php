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

use Tygh\NotificationsCenter\IFactory;

/**
 * Class DefaultNotificationBulder builds on-site notifications from their data.
 *
 * @package Tygh\NotificationsCenter\NotificationBuilders
 */
class DefaultNotificationBulder implements INotificationBuilder
{
    /**
     * @var \Tygh\NotificationsCenter\IFactory
     */
    protected $factory;

    public function __construct(IFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createNotification($params, $area, $lang_code)
    {
        return $this->factory->fromArray($params);
    }
}
