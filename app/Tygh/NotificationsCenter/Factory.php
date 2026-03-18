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

use Tygh\Application;
use Tygh\Enum\NotificationSeverity;
use Tygh\Exceptions\DeveloperException;
use Tygh\NotificationsCenter\NotificationBuilders\DBTemplateNotificationBuilder;
use Tygh\NotificationsCenter\NotificationBuilders\DefaultNotificationBulder;

/**
 * Class Factory creates Notifications.
 *
 * @package Tygh\NotificationsCenter
 */
class Factory implements IFactory
{
    /**
     * @var \Tygh\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /** @inheritdoc */
    public function fromArray(array $data)
    {
        $data = array_merge([
            'notification_id' => 0,
            'user_id'         => 0,
            'title'           => '',
            'message'         => '',
            'severity'        => NotificationSeverity::NOTICE,
            'section'         => 'administration',
            'tag'             => '',
            'area'            => 'A',
            'action_url'      => '',
            'is_read'         => false,
            'timestamp'       => time(),
            'pinned'          => false,
            'remind'          => false
        ], $data);

        $data['is_read'] = (bool) $data['is_read'];

        $notification = new Notification(
            $data['notification_id'],
            $data['user_id'],
            $data['title'],
            $data['message'],
            $data['severity'],
            $data['section'],
            $data['tag'],
            $data['area'],
            $data['action_url'],
            $data['is_read'],
            $data['timestamp'],
            $data['pinned'],
            $data['remind']
        );

        return $notification;
    }

    /** @inheritdoc */
    public function getNotificationBuilder($type)
    {
        switch ($type) {
            case 'db_template':
                return new DBTemplateNotificationBuilder(
                    $this,
                    $this->app['template.renderer'],
                    $this->app['template.internal.repository']
                );
                break;
            case 'default':
                return new DefaultNotificationBulder(
                    $this
                );
                break;
            default:
                throw new DeveloperException("Undefined notification builder: {$type}");
                break;
        }
    }
}
