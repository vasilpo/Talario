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

/** @var string $mode */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'add') {
        if (
            !empty($dispatch_extra)
            && !empty($_REQUEST['product_id'])
            && !empty($_REQUEST['product_data'])
        ) {
            $master_product_id = $_REQUEST['product_id'];

            if (
                !isset($_REQUEST['product_data'][$dispatch_extra]['product_options'])
                && !empty($_REQUEST['product_data'][$master_product_id]['product_options'])
            ) {
                $_REQUEST['product_data'][$dispatch_extra]['product_options'] = $_REQUEST['product_data'][$master_product_id]['product_options'];
            }

            if (!empty($_REQUEST['product_data']['custom_files'])) {
                foreach ($_REQUEST['product_data']['custom_files'] as $files_key => $files_value) {
                    $_REQUEST['product_data']['custom_files'][$files_key] = str_replace($master_product_id, $dispatch_extra, $files_value);
                }
            }
        }
    }
}
