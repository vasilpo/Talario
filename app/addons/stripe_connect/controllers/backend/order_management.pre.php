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

/** @var array{REQUEST_METHOD: string} $_SERVER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'place_order') {
    // This workaround is used to save current payment intent ID when order is updated with no payment data
    if (
        isset($_REQUEST['payment_info']['stripe_connect.payment_intent_id'])
        && empty($_REQUEST['payment_info']['stripe_connect.payment_intent_id'])
    ) {
        unset($_REQUEST['payment_info']['stripe_connect.payment_intent_id']);
    }
}
