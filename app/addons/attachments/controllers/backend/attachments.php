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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (Registry::get('runtime.company_id')) {
    $permission = fn_attachments_check_permission($_REQUEST);
    if (!$permission) {
        fn_set_notification('W', __('warning'), __('access_denied'));
        if (defined('AJAX_REQUEST')) {
            exit;
        } else {
            return array(CONTROLLER_STATUS_DENIED);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // Create/update attachments
    //
    if ($mode == 'update') {
        if (!empty($_REQUEST['attachment_data'])) {
            fn_update_attachments(
                $_REQUEST['attachment_data'],
                $_REQUEST['attachment_id'],
                $_REQUEST['object_type'],
                $_REQUEST['object_id'],
                'M',
                [],
                DESCR_SL
            );
        }
    }

    if ($mode == 'delete') {
        fn_delete_attachments(array($_REQUEST['attachment_id']), $_REQUEST['object_type'], $_REQUEST['object_id']);
        $attachments = fn_get_attachments($_REQUEST['object_type'], $_REQUEST['object_id']);
        if (empty($attachments)) {
            Tygh::$app['view']->assign('object_type', $_REQUEST['object_type']);
            Tygh::$app['view']->assign('object_id', $_REQUEST['object_id']);
            Tygh::$app['view']->display('addons/attachments/views/attachments/manage.tpl');
        }
        exit;
    }

    return array(CONTROLLER_STATUS_OK); // redirect should be performed via redirect_url always
}

if ($mode == 'getfile') {
    if (!empty($_REQUEST['attachment_id'])) {
        fn_get_attachment($_REQUEST['attachment_id']);
    }
    exit;

} elseif ($mode == 'update') {
    // Assign attachments files for products
    $attachments = fn_get_attachments($_REQUEST['object_type'], $_REQUEST['object_id'], 'M', DESCR_SL);

    Registry::set('navigation.tabs.attachments', array (
        'title' => __('attachments'),
        'js' => true
    ));

    Tygh::$app['view']->assign('attachments', $attachments);
}
