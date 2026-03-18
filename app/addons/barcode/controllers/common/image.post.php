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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode == 'barcode') {
    $value_to_encode = empty($_REQUEST['id']) ? null : $_REQUEST['id'];
    $width = empty($_REQUEST['width']) ? null : $_REQUEST['width'];
    $height = empty($_REQUEST['height']) ? null : $_REQUEST['height'];
    $algorithm = empty($_REQUEST['type']) ? null : $_REQUEST['type'];

    $max_width = Registry::ifGet('config.lazy_thumbnails.max_width', $width);
    $max_height = Registry::ifGet('config.lazy_thumbnails.max_height', $height);

    if ($max_width < Registry::get('addons.barcode.width')) {
        $max_width = Registry::get('addons.barcode.width');
    }

    if ($max_height < Registry::get('addons.barcode.height')) {
        $max_height = Registry::get('addons.barcode.height');
    }

    if ($width > $max_width || $height > $max_height) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }

    $barcode_prefix = Registry::get('addons.barcode.prefix');

    $value_to_encode = $barcode_prefix . $value_to_encode;

    $output_image_format = Registry::get('addons.barcode.output');
    $print_text_on_image = (Registry::get('addons.barcode.text') == 'Y');

    /** @var \Tygh\Tools\Barcode\Generator $barcode_generator */
    $barcode_generator = Tygh::$app['addons.barcode.generator'];

    $image = $barcode_generator->createBarcode(
        (int) $width, (int) $height,
        $value_to_encode,
        $algorithm,
        $print_text_on_image
    );

    header("Content-type: image/{$output_image_format}");

    echo $image->get($output_image_format, array(
        'flatten' => true,
        'png_compression_level' => 7,
        'jpeg_quality' => 90,
    ));

    exit;
}
