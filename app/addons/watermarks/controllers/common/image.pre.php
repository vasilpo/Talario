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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Settings;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete image
    if ($mode === 'delete_image') {
        if (SiteArea::isAdmin(AREA) && !empty($auth['user_id'])) {
            if ($_REQUEST['object_type'] === 'watermark' || $_REQUEST['object_type'] === 'detailed') {
                $images_data = db_get_row(
                    'SELECT image_id, detailed_id FROM ?:images_links WHERE pair_id = ?i AND object_type = ?s',
                    $_REQUEST['pair_id'],
                    'watermark'
                );

                if (!empty($images_data) && (!fn_allowed_for('ULTIMATE') || Registry::get('runtime.company_id'))) {
                    if ($_REQUEST['image_id'] === $images_data['image_id']) {
                        $type = 'icons';
                    } elseif ($_REQUEST['image_id'] === $images_data['detailed_id']) {
                        $type = 'detailed';
                    }

                    if (!empty($type)) {
                        $option_types = fn_get_apply_watermark_options();
                        $is_unset = false;
                        foreach ($option_types[$type] as $name => $option_id) {
                            if (Settings::instance()->getValue($name, '') === YesNo::YES) {
                                Settings::instance()->updateValueById($option_id, YesNo::NO);
                                $is_unset = true;
                            }
                        }

                        if ($is_unset) {
                            fn_set_notification(NotificationSeverity::ERROR, __('error'), __('wt_fail_apply_graphic_watermark', [
                                '[image_type]' => __('wt_' . $type)
                            ]));

                            fn_delete_watermarks([$type => true]);
                        }
                    }
                }
            }
        }
    }
}
