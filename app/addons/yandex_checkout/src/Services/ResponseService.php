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

namespace Tygh\Addons\YandexCheckout\Services;

use Tygh\Addons\YandexCheckout\Enum\PaymentStatus;
use YooKassa\Model\Metadata;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

/**
 * Class ResponseService allows to work with responses from Yandex.Checkout API
 *
 * @package Tygh\Addons\YandexCheckout\Services
 */
class ResponseService
{
    /**
     * @param $response
     *
     * @return \YooKassa\Model\Metadata
     */
    public function getMetadataFromNotification($response)
    {
        $notification = $this->getNotificationFromResponse($response);
        if (!$notification) {
            return new Metadata();
        }
        $request_object = $notification->getObject();
        return $request_object->getMetadata();
    }

    /**
     * @param $response
     *
     * @return \YooKassa\Model\Notification\NotificationCanceled|\YooKassa\Model\Notification\NotificationWaitingForCapture|\YooKassa\Model\Notification\NotificationSucceeded|null
     */
    protected function getNotificationFromResponse($response)
    {
        $request_body = $this->getRequest($response);
        switch ($request_body['event']) {
            case NotificationEventType::PAYMENT_SUCCEEDED:
                $notification = new NotificationSucceeded($request_body);
                break;
            case NotificationEventType::PAYMENT_CANCELED:
                $notification = new NotificationCanceled($request_body);
                break;
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE:
                $notification = new NotificationWaitingForCapture($request_body);
                break;
            default:
                return null;
        }
        return $notification;
    }

    /**
     * @param $response
     *
     * @return string|null
     */
    public function getPaymentIdFromNotification($response)
    {
        $notification = $this->getNotificationFromResponse($response);
        if (!$notification) {
            return null;
        }
        $response_object = $notification->getObject();
        return $response_object->getId();
    }

    /**
     * @param $response
     *
     * @return string
     */
    public function getStatusFromNotification($response)
    {
        $request_body = $this->getRequest($response);
        switch ($request_body['event']) {
            case NotificationEventType::PAYMENT_SUCCEEDED:
                return PaymentStatus::SUCCEEDED;
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE:
                return PaymentStatus::WAITING_FOR_CAPTURE;
            case NotificationEventType::PAYMENT_CANCELED:
            default:
                return PaymentStatus::CANCELED;
        }
    }

    /**
     * @param $response
     *
     * @return mixed
     */
    protected function getRequest($response)
    {
        return json_decode($response, true);
    }
}