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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode === 'get_services_list') {

    if (!empty($_REQUEST['object_id'])
        && defined('AJAX_REQUEST')
    ) {
        $shipping = !empty($_REQUEST['shipping_data']) ? $_REQUEST['shipping_data'] : array();
        $sending_services = fn_rus_russianpost_get_shipping_services_by_sending_object($_REQUEST['object_id']);

        /** @var Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        $view->assign(array(
            'sending_services' => $sending_services,
            'shipping' => $shipping,
        ));

        $view->display('addons/rus_russianpost/views/shippings/components/services/russian_post_services.tpl');

        exit;
    }
}
