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

use Tygh\Addons\PaypalCommercePlatform\Enum\CaptureStatus;
use Tygh\Enum\OrderStatuses;
use Tygh\Registry;

return [
    CaptureStatus::COMPLETED          => OrderStatuses::PAID,
    CaptureStatus::PENDING            => OrderStatuses::OPEN,
    CaptureStatus::DECLINED           => OrderStatuses::CANCELED,
    CaptureStatus::PARTIALLY_REFUNDED => Registry::get('addons.paypal_commerce_platform.rma_refunded_order_status'),
    CaptureStatus::REFUNDED           => Registry::get('addons.paypal_commerce_platform.rma_refunded_order_status'),
];
