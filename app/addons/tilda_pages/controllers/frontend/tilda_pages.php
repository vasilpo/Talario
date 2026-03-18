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

use Tygh\Addons\TildaPages\ServiceProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'import') {
    if (
        empty($_REQUEST['publickey'])
        || empty(Registry::get('addons.tilda_pages.tilda_public_api_key'))
        || $_REQUEST['publickey'] !== Registry::get('addons.tilda_pages.tilda_public_api_key')
    ) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    if (empty($_REQUEST['pageid'])) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $tilda_client = ServiceProvider::getTildaClient();

    $external_page_data = $tilda_client->getFullExportedPageById($_REQUEST['pageid']);

    if (empty($external_page_data)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $tilda_page_id = $external_page_data['id'];
    $tilda_page = fn_tilda_pages_get_tilda_page_data($tilda_page_id);

    if (empty($tilda_page)) {
        $tilda_page = fn_tilda_pages_get_tilda_page_data($tilda_page_id, [], 'tilda_locations');
    }

    if (empty($tilda_page)) {
        fn_echo('ok');
        exit();
    }

    $additional_data = [
        'inner_page_id'     => $tilda_page['inner_page_id'] ?? null,
        'inner_location_id' => $tilda_page['inner_location_id'] ?? null,
        'is_only_content'   => $tilda_page['is_only_content']
    ];
    $page = fn_tilda_pages_convert_data($external_page_data, $additional_data);

    if (isset($page['inner_page_id'])) {
        fn_tilda_pages_update_tilda_page($page, true, 'tilda_pages');
    } elseif (isset($page['inner_location_id'])) {
        fn_tilda_pages_update_tilda_page($page, true, 'tilda_locations');
    }

    return [CONTROLLER_STATUS_NO_CONTENT];
}
