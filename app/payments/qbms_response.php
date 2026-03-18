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

if (!defined('BOOTSTRAP')) {
    if (!empty($_REQUEST['conntkt'])) {

        // Set the connection ticket to the payment params
        define('AREA', 'A');
        require dirname(__FILE__) . '/../../init.php';
        if (!fn_check_prosessor_status('qbms')) {
            die('Access denied');
        }
        $payments = db_get_fields("SELECT a.payment_id FROM ?:payments as a LEFT JOIN ?:payment_processors as b ON b.processor_id = a.processor_id WHERE b.processor_script = 'qbms.php'");
        foreach ($payments as $payment_id) {
            $processor_data = fn_get_payment_method_data($payment_id);
            if ($processor_data["processor_params"]["app_id"] == $_REQUEST['appid']) {
                $processor_data["processor_params"]["connection_ticket"] = $_REQUEST['conntkt'];
                $_data = array (
                    'processor_params' => serialize($processor_data['processor_params']),
                );
                db_query("UPDATE ?:payments SET ?u WHERE payment_id = ?i", $_data, $payment_id);
            }
        }
    } else {
        die('Access denied');
    }
}
