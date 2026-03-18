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

namespace Tygh\Addons\Tinkoff\Enum;

defined('BOOTSTRAP') or die('Access denied');

class PaymentSessionStatuses
{
    const AUTHORIZED       = 'AUTHORIZED';
    const CANCELED         = 'CANCELED';
    const CONFIRMED        = 'CONFIRMED';
    const NEW              = 'NEW';
    const PARTIAL_REFUNDED = 'PARTIAL_REFUNDED';
    const PARTIAL_REVERSED = 'PARTIAL_REVERSED';
    const REFUNDED         = 'REFUNDED';
    const REJECTED         = 'REJECTED';
    const REVERSED         = 'REVERSED';
}
