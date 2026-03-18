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

define('WATERMARK_FONT_ALPHA', 50);
define('WATERMARK_NONE', 'N');
define('WATERMARK_DISABLED', 'D');
define('WATERMARK_NONCREATED', 'A');
define('WATERMARK_FAILED', 'F');
define('WATERMARK_CREATED', 'Y');
define('WATERMARK_TYPE_TEXT', 'T');
define('WATERMARK_TYPE_GRAPHIC', 'G');
define('WATERMARK_PADDING', 3);

fn_register_hooks(
    'attach_absolute_image_paths',
    'delete_image',
    'update_image',
    'init_company_data',
    'generate_thumbnail_file_pre',
    'generate_thumbnail_post',
    ['get_route', 1],
    'update_company',
    'image_zoom_check_image_post'
);
