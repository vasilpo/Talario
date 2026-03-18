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

use Tygh\Common\OperationResult;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var \Tygh\Ajax $ajax */
$ajax = Tygh::$app['ajax'];

/** @var array $auth */
$auth = Tygh::$app['session']['auth'];

/** @var \Tygh\NotificationsCenter\NotificationsCenter $notifications_center */
$notifications_center = Tygh::$app['notifications_center'];

/** @var \Tygh\NotificationsCenter\Repository $notifications_center_repository */
$notifications_center_repository = Tygh::$app['notifications_center.repository'];

if (empty($auth['user_id'])) {
    $view_data = $notifications_center->buildViewData([]);
    $ajax->assign('notifications_center', $view_data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'set_read') {
        $params = array_merge([
            'is_read'          => true,
            'notification_ids' => [],
        ], $_REQUEST);

        $result = new OperationResult(false);
        if ($params['notification_ids']) {
            $result = $notifications_center->setRead($params['notification_ids'], $params['is_read']);
        }

        $ajax->assign('result', $result->isSuccess());
        if (!$result->isSuccess()) {
            $result->showNotifications();
        }
    } elseif ($mode === 'dismiss') {
        $params = array_merge([
            'notification_ids' => [],
        ], $_REQUEST);

        $result = new OperationResult(false);
        if ($params['notification_ids']) {
            $result = $notifications_center->dismiss($params['notification_ids']);
        }

        $ajax->assign('result', $result->isSuccess());
        if (!$result->isSuccess()) {
            $result->showNotifications();
        }
    } elseif ($mode === 'set_all_as_read') {
        $result = $notifications_center->setAllAsRead($auth['user_id']);

        $ajax->assign('result', $result->isSuccess());
        if (!$result->isSuccess()) {
            $result->showNotifications();
        }
    }
    exit;
}

if ($mode === 'manage') {
    // Reminds about older notifications
    $notifications_center->remind();

    /** @var \Tygh\NotificationsCenter\Notification[] $notifications */
    $params = array_merge([
        'items_per_page' => null,
        'sort_by'        => 'pinned_timestamp'
    ], $_REQUEST);

    $notifications = $notifications_center->get($params, $params['items_per_page']);

    // We also show pinned notifications in pop-ups.
    $pinned_notifications = array_filter($notifications, static function ($notify) {
        return $notify->pinned && !$notify->is_read;
    });

    $notifications_center->showNotificationsInPopup($pinned_notifications);

    /** @var \Tygh\NotificationsCenter\Notification $pinned_notification */
    foreach ($pinned_notifications as $pinned_notification) {
        $pinned_notification->is_read = true;
        $notifications_center_repository->save($pinned_notification);
    }

    $view_data = $notifications_center->buildViewData($notifications);
    $ajax->assign('notifications_center', $view_data);
}
exit;
