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
use Tygh\Addons\ProductVariations\Product\Type\Type;

defined('BOOTSTRAP') or die('Access denied');

function fn_settings_actions_general_show_out_of_stock_products(&$new_value, $old_value)
{
    fn_set_notification(NotificationSeverity::WARNING, __('warning'), __('master_products.resave_after_show_out_of_stock_products_changed', [
        '[url]' => fn_url('products.manage?product_type=' . Type::PRODUCT_TYPE_SIMPLE)
    ]));

    return true;
}
