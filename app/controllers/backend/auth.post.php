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

use Tygh\Enum\OrderStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'login') {
        fn_batch_cleanup_payment_info(array(
            'time_from' => TIME - 7 * SECONDS_IN_DAY,
            'time_to'   => TIME - SECONDS_IN_HOUR,
            'status'    => OrderStatuses::INCOMPLETED,
        ));

        $ttl_keys = db_get_fields('SELECT data_key FROM ?:storage_data WHERE data_key LIKE ?l AND CAST(data AS UNSIGNED) < ?i', '%_ttl', TIME);

        foreach ($ttl_keys as $ttl_key) {
            $data_key = substr($ttl_key, 0, -strlen('_ttl'));

            db_query('DELETE FROM ?:storage_data WHERE data_key = ?s OR data_key = ?s', $ttl_key, $data_key);

            Registry::del('storage_data.' . $ttl_key);
            Registry::del('storage_data.' . $data_key);
        }
    }

    return array(CONTROLLER_STATUS_OK);
}