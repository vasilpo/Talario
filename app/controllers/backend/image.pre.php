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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'upload') {
        $rebuilt = fn_rebuild_files('file');
        $file = reset($rebuilt);

        if (empty($file)) {
            exit;
        }

        $file_extension = fn_get_file_ext($file['name']);

        if (!fn_is_file_extension_allowed($file_extension)) {
            exit;
        }

        if (!fn_is_image_file_size_allowed($file['size'])) {
            exit;
        }

        $file = fn_move_uploaded_file($file);

        Tygh::$app['ajax']->assign('local_data', $file);
        exit;
    }

    return [CONTROLLER_STATUS_OK];
}

