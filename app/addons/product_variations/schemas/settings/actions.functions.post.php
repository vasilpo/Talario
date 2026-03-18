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

use Tygh\Addons\ProductVariations\Product\Type\Type;
use Tygh\Addons\ProductVariations\ServiceProvider;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\YesNo;
use Tygh\Tools\Url;

defined('BOOTSTRAP') or die('Access denied');

function fn_settings_actions_addons_product_variations_variations_allow_own_features(&$new_value, $old_value)
{
    if ($old_value && $new_value != $old_value && $new_value == YesNo::NO) {
        $group_ids = ServiceProvider::getGroupRepository()->findAllGroupIds();

        if (!$group_ids) {
            return true;
        }

        $search_link = Url::buildUrn(['products', 'manage'], [
            'variation_group_id' => $group_ids,
            'product_type'       => Type::PRODUCT_TYPE_SIMPLE
        ]);

        fn_set_notification(
            NotificationSeverity::WARNING, __('warning'),
            __('product_variations.allow_own_features_setting_changing', [
            '[url]' => fn_url($search_link, 'A')
        ]));
    }

    return true;
}

function fn_settings_actions_addons_product_variations_variations_allow_own_images(&$new_value, $old_value)
{
    if ($old_value && $new_value != $old_value && $new_value == YesNo::NO) {
        $group_ids = ServiceProvider::getGroupRepository()->findAllGroupIds();

        if (!$group_ids) {
            return true;
        }

        $search_link = Url::buildUrn(['products', 'manage'], [
            'variation_group_id' => $group_ids,
            'product_type'       => Type::PRODUCT_TYPE_SIMPLE
        ]);

        fn_set_notification(
            NotificationSeverity::WARNING, __('warning'),
            __('product_variations.allow_own_images_setting_changing', [
            '[url]' => fn_url($search_link, 'A')
        ]));
    }

    return true;
}