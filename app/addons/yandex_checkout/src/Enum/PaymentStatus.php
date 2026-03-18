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

namespace Tygh\Addons\YandexCheckout\Enum;

/**
 * Class PaymentStatus represents all available payment statuses in YooKassa API
 *
 * @package Tygh\Addons\YandexCheckout\Enum
 */
class PaymentStatus
{
    const PENDING = 'pending';
    const WAITING_FOR_CAPTURE = 'waiting_for_capture';
    const SUCCEEDED = 'succeeded';
    const CANCELED = 'canceled';
}