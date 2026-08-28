<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if (!fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_DENIED];
}

$humanize_notification = static function ($value) {
    $value = (string) $value;
    $replace = [
        'администрацией магазина' => 'Talario',
        'администрация магазина' => 'Talario',
        'администрации магазина' => 'Talario',
        'панелью администратора' => 'кабинетом партнёра',
        'каталог товаров' => 'витрину Talario',
        'каталоге товаров' => 'витрине Talario',
        'в магазине' => 'на витрине Talario',
        'Ваш товар' => 'Ваше занятие',
        'ваш товар' => 'ваше занятие',
        'Товар' => 'Занятие',
        'товар' => 'занятие',
        'Товары' => 'Занятия',
        'товары' => 'занятия',
        'товаров' => 'занятий',
        'товара' => 'занятия',
        'покупатели' => 'родители',
    ];

    return strtr($value, $replace);
};

if ($mode === 'manage') {
    $notifications_center = Tygh::$app['notifications_center'];
    $notifications = $notifications_center->get([
        'sort_by' => 'pinned_timestamp',
    ], 50);

    $items = [];
    foreach ($notifications as $notification) {
        $item = $notification->toArray();
        $item['title'] = $humanize_notification($item['title'] ?? '');
        $item['message'] = $humanize_notification($item['message'] ?? '');
        $item['action_url'] = $notifications_center->getActionUrl($notification->action_url, $notification->area);

        if (preg_match('/products\.update[^?]*\?[^#]*product_id=(\d+)/', (string) $item['action_url'], $matches)) {
            $item['action_url'] = fn_url('talario_classes.update?product_id=' . (int) $matches[1]);
        }

        $items[] = $item;
    }

    Tygh::$app['view']->assign([
        'talario_notifications' => $items,
        'talario_notifications_count' => count($items),
    ]);
}
