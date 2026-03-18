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

use Tygh\Tygh;

/** @var string $mode */
if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && $mode === 'update'
    && $_REQUEST['addon'] === 'paypal_commerce_platform'
) {
    /** @var array $options */
    $options = Tygh::$app['view']->getTemplateVars('options');

    foreach ($options['general'] as $setting_id => $option_item) {
        if ($option_item['name'] == 'rma_refunded_order_status') {
            Tygh::$app['view']->assign('rma_refunded_order_status_id', $setting_id);
        }
    }

    Tygh::$app['view']->assign('order_statuses', fn_get_simple_statuses(STATUSES_ORDER));
}