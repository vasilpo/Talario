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

/** @var string $mode */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'details') {
    /** @var array $order_info */
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');

    Tygh::$app['view']->assign(
        'is_show_transfer_funds_button',
        $order_info && fn_stripe_connect_is_allowed_transfer_funds_by_order_info($order_info)
    );

    $is_stripe_connect_payment = isset($order_info['payment_method']['processor_params']['is_stripe_connect']);
    Tygh::$app['view']->assign('is_stripe_connect_payment', $is_stripe_connect_payment);
}
