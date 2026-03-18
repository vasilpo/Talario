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

use Tygh\Languages\Languages;

function fn_settings_actions_addons_banners_banner_multilang($new_value, $old_value)
{
    if ($new_value == 'N') {
        $lang_codes = Languages::getAll();
        unset($lang_codes[DEFAULT_LANGUAGE]);

        $banners_multilang = array();
        foreach ($lang_codes as $lang_code => $lang_data) {
            list($banners) = fn_get_banners(array(), $lang_code);

            foreach ($banners as $banner) {
                $banners_multilang[$lang_code][$banner['banner_id']] = $banner;
            }
        }

        list($banners) = fn_get_banners(array(), DEFAULT_LANGUAGE);

        foreach ($banners as $banner) {
            if ($banner['type'] != 'G') {
                continue;
            }

            $main_image_id = !empty($banner['main_pair']['image_id']) ? $banner['main_pair']['image_id'] : 0;

            foreach ($lang_codes as $lang_code => $lang_data) {
                $banner_lang = $banners_multilang[$lang_code][$banner['banner_id']];
                $lang_image_id = !empty($banner_lang['main_pair']['image_id']) ? $banner_lang['main_pair']['image_id'] : 0;

                if ($lang_image_id != 0 && ($main_image_id == 0 || $main_image_id != $lang_image_id)) {
                    fn_delete_image($lang_image_id, $banner_lang['main_pair']['pair_id'], 'promo');
                    $lang_image_id = 0;
                }

                if ($lang_image_id == 0 && $main_image_id != 0) {
                    $data_banner_image = array(
                        'banner_id' => $banner['banner_id'],
                        'lang_code' => $lang_code
                    );
                    $banner_image_id = db_query("INSERT INTO ?:banner_images ?e", $data_banner_image);
                    fn_add_image_link($banner_image_id, $banner['main_pair']['pair_id']);

                    $data_desc = array (
                        'description' => empty($banner['main_pair']['icon']['alt']) ? '' : $banner['main_pair']['icon']['alt'],
                        'object_holder' => 'images'
                    );

                    fn_create_description('common_descriptions', 'object_id', $main_image_id, $data_desc);
                }

                db_query("UPDATE ?:banner_descriptions SET url = ?s WHERE banner_id = ?i", $banner['url'], $banner['banner_id']);
            }

        }

    }

    return true;
}
