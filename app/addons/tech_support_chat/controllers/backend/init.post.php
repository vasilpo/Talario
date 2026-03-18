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

defined('BOOTSTRAP') or die('Access denied');

if (isset(Tygh::$app['session']['license_information'])) {
    $license = Tygh::$app['session']['license_information'];

    if (strpos($license, '<?xml') !== false) {
        $license = simplexml_load_string($license);

        if (isset($license->OnlineTechSupportWidgetId)) {
            $tech_support_chat_widget_id = (string) $license->OnlineTechSupportWidgetId;
            fn_set_storage_data('tech_support_chat_widget_id', $tech_support_chat_widget_id);
        } else {
            fn_set_storage_data('tech_support_chat_widget_id');
        }
    }
}

Tygh::$app['view']->assign('tech_support_chat_widget_id', fn_get_storage_data('tech_support_chat_widget_id'));

return [CONTROLLER_STATUS_OK];
