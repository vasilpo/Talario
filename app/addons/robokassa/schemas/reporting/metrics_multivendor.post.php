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

use Tygh\Addons\Robokassa\Payments\RobokassaSplit;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['robokassa'] = static function () {
    $processor_data = fn_get_processor_data_by_name('robokassa.php');

    if ($processor_data) {
        $payment_ids = db_get_fields(
            'SELECT payment_id FROM ?:payments WHERE status = ?s AND processor_id = ?i',
            ObjectStatuses::ACTIVE,
            $processor_data['processor_id']
        );

        foreach ($payment_ids as $payment_id) {
            $data = fn_get_processor_data($payment_id);

            if (
                !empty($data['processor_params']['merchantid'])
                && !empty($data['processor_params']['password1'])
                && !empty($data['processor_params']['password2'])
            ) {
                return true;
            }
        }
    }

    return false;
};

$schema['robokassa_split'] = static function () {
    $processor_data = fn_get_processor_data_by_name(RobokassaSplit::PROCESSOR_SCRIPT);

    if ($processor_data) {
        $payment_ids = db_get_fields(
            'SELECT payment_id FROM ?:payments WHERE status = ?s AND processor_id = ?i',
            ObjectStatuses::ACTIVE,
            $processor_data['processor_id']
        );

        foreach ($payment_ids as $payment_id) {
            $data = fn_get_processor_data($payment_id);

            if (!empty($data['processor_params']['master_store_id'])) {
                return true;
            }
        }
    }

    return false;
};

return $schema;
