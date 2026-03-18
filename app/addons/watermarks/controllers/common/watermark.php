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

use Tygh\Storage;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//
// Delete image
//
if ($mode == 'create') {

    $result_image = '';

    if (!empty($_SERVER['REQUEST_URI'])) {
        $path = defined('HTTPS') ? Registry::get('config.https_path') : Registry::get('config.http_path');

        $image_file = str_replace($path . '/images/', '', $_SERVER['REQUEST_URI']);
        $watermarked_file = WATERMARKS_DIR_NAME . $image_file;

        if (Storage::instance('images')->isExist($watermarked_file)) {
            $result_image = Storage::instance('images')->getUrl($watermarked_file);

        } elseif (Storage::instance('images')->isExist($image_file)) {
            $image_name = fn_basename($image_file);
            $image_id = db_get_field("SELECT image_id FROM ?:images WHERE image_path LIKE ?l", "%$image_name%");
            $image_link = db_get_row("SELECT * FROM ?:images_links WHERE image_id = ?i OR detailed_id = ?i", $image_id, $image_id);

            if (!empty($image_link)) {
                $is_detailed = ($image_link['detailed_id'] == $image_id);
                $image_type = $is_detailed ? 'detailed' : 'icons';

                $generate_watermark = fn_is_need_watermark($image_link['object_type'], $is_detailed, Registry::get('runtime.company_id'));

                if (fn_watermark_create($image_file, $watermarked_file, $is_detailed, Registry::get('runtime.company_id'), $generate_watermark)) {
                    $result_image = Storage::instance('images')->getUrl($watermarked_file);
                }
            }
        }
    }

    if (!empty($result_image)) {
        header('Location: ' . $result_image);
    }
    exit;

}
