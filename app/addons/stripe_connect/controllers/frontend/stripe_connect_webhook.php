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

use Tygh\Addons\StripeConnect\Webhook\StripeWebhook;
use Stripe\Webhook;

defined('BOOTSTRAP') or die('Access denied');

if (!isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
    die('Access denied');
}

$payload = @file_get_contents('php://input');
$stripe_signature = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = $id = null;

if (!empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
} else {
    $event_data = @json_decode($payload, true);
    $webhooks = fn_get_schema('stripe_connect', 'webhooks');
    foreach ($webhooks as $_id => $webhook_params) {
        if (in_array($event_data['type'], $webhook_params['enabled_events'])) {
            $id = $_id;
            break;
        }
    }
}

try {
    $event = Webhook::constructEvent(
        $payload,
        $stripe_signature,
        StripeWebhook::getSecretKey((string) $id)
    );
} catch (\Exception $e) {
    return [CONTROLLER_STATUS_NO_CONTENT];
}

StripeWebhook::handle($event);

return [CONTROLLER_STATUS_NO_CONTENT];
