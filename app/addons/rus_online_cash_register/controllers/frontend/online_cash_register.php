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


use Tygh\Addons\RusOnlineCashRegister\CashRegister\Atol\InfoResponse;
use Tygh\Addons\RusOnlineCashRegister\RequestLogger;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var string $mode */

if ($mode === 'callback_atol') {
    $data = file_get_contents('php://input');

    if (!$data) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }

    /** @var Tygh\Addons\RusOnlineCashRegister\RequestLogger $request_logger */
    $request_logger = Tygh::$app['addons.rus_online_cash_register.request_logger'];

    /** @var Tygh\Addons\RusOnlineCashRegister\ReceiptRepository $receipt_repository */
    $receipt_repository = Tygh::$app['addons.rus_online_cash_register.receipt_repository'];

    /** @var Tygh\Addons\RusOnlineCashRegister\Service $service */
    $service = Tygh::$app['addons.rus_online_cash_register.service'];

    $request_logger->log('callback', null, $data, RequestLogger::STATUS_SUCCESS);

    $response = new InfoResponse($data);
    $uuid = $response->getUUID();

    if ($uuid) {
        $receipt = $receipt_repository->findByUUID($uuid);
        $service->updateReceiptByInfoResponse($receipt, $response);

        exit();
    }

    return array(CONTROLLER_STATUS_NO_PAGE);
}
