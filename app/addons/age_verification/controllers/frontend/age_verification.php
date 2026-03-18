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
    if ($mode == 'verify') {
        if (!empty($_REQUEST['age'])) {
            $age = intval($_REQUEST['age']);

            if ($age < 0) {
                $age = 0;
            }

            Tygh::$app['session']['auth']['age'] = $age;

            if (!empty($_REQUEST['redirect_url'])) {
                return array (CONTROLLER_STATUS_OK, $_REQUEST['redirect_url']);
            }

            return array (CONTROLLER_STATUS_REDIRECT, '');
        }
    }
}

if ($mode == 'verify') {
    fn_add_breadcrumb(__('age_verification'));

    $available_objects = array(
        'product_descriptions' => 'product_id',
        'category_descriptions' => 'category_id'
    );

    if (isset($_REQUEST['object'], $_REQUEST['object_id'])
        && isset($available_objects[$_REQUEST['object']])
    ) {
        $table = $_REQUEST['object'];
        $field = $available_objects[$table];
        $where = array(
            $field => $_REQUEST['object_id'],
            'lang_code' => CART_LANGUAGE,
        );

        $message = db_get_field('SELECT age_warning_message FROM ?:?p WHERE ?w', $table, $where);

        Tygh::$app['view']->assign('age_warning_message', $message);
    }

    if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'deny') {
        Tygh::$app['view']->assign('age_warning_type', 'deny');
    } else {
        Tygh::$app['view']->assign('age_warning_type', 'form');
    }
}
