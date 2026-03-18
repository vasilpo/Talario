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
use Tygh\Enum\ObjectStatuses;

function fn_settings_actions_addons_searchanise($new_status, $old_status)
{
    $class_loader = Tygh::$app['class_loader'];
    $class_loader->add('', Registry::get('config.dir.addons') . 'searchanise');

    if (fn_se_is_registered() == true) {
        if ($new_status === ObjectStatuses::ACTIVE) {
            fn_se_signup();
            fn_se_queue_import();
        }
    }

    return true;
}
