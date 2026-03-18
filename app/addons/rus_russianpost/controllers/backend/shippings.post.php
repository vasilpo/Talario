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

if ($mode == 'configure') {
    if ($_REQUEST['module'] == 'russian_post') {
        $sending_packages = fn_get_schema('russianpost', 'sending_packages', 'php', true);
        $sending_objects = fn_get_schema('russianpost', 'sending_objects', 'php', true);

        /** @var Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        $shipping = $view->getTemplateVars('shipping');

        $selected_object_id = fn_rus_russianpost_get_selected_object($shipping, $sending_objects);
        $sending_services = fn_rus_russianpost_get_shipping_services_by_sending_object($selected_object_id);

        $view->assign(array(
            'sending_packages' => $sending_packages['send_packages'],
            'sending_objects' => $sending_objects,
            'sending_services' => $sending_services,
        ));
    }
}
