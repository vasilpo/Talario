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

/** @var array<string, callable> $schema */
$schema['paypal_commerce_platform'] = static function () {
    $processor_data = fn_get_processor_data_by_name('paypal_commerce_platform.php');

    if ($processor_data) {
        $payment_ids = db_get_fields(
            'SELECT payment_id FROM ?:payments WHERE status = ?s AND processor_id = ?i',
            'A',
            $processor_data['processor_id']
        );

        foreach ($payment_ids as $payment_id) {
            $data = fn_get_processor_data($payment_id);

            if (
                empty($data['processor_params']['client_id'])
                || empty($data['processor_params']['secret'])
                || empty($data['processor_params']['payer_id'])
            ) {
                continue;
            }

            return true;
        }
    }

    return false;
};

return $schema;
