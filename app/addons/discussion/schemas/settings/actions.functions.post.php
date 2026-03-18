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

if (Registry::get('addons.discussion.status') !== 'A') {
    /** workaround (see 1-22725 1-13030) */
    /** @var \Composer\Autoload\ClassLoader $class_loader */
    $class_loader = Tygh::$app['class_loader'];
    $class_loader->add('', Registry::get('config.dir.addons') . 'discussion');
}

/**
 * Check if mod_rewrite is active and clean up templates cache
 */
function fn_settings_actions_addons_discussion_home_page_testimonials(&$new_value, $old_value)
{
    if (function_exists('fn_create_empty_thread')) {
        fn_create_empty_thread($new_value);
    }

    return true;
}

function fn_settings_actions_addons_discussion_company_discussion_type(&$new_value, $old_value)
{
    db_query('UPDATE ?:discussion SET type = ?s WHERE object_type = ?s', $new_value, 'M');
}

function fn_settings_variants_addons_discussion_product_discussion_type()
{
    return fn_discussion_get_discussion_types();
}

function fn_settings_variants_addons_discussion_category_discussion_type()
{
    return fn_discussion_get_discussion_types();
}

function fn_settings_variants_addons_discussion_page_discussion_type()
{
    return fn_discussion_get_discussion_types();
}

function fn_settings_variants_addons_discussion_home_page_testimonials()
{
    return fn_discussion_get_discussion_types();
}

function fn_settings_variants_addons_discussion_company_discussion_type()
{
    return fn_discussion_get_discussion_types();
}