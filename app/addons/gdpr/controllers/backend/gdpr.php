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

use Tygh\Addons\Gdpr\DataExtractor\IDataExtractor;
use Tygh\Storage;

defined('BOOTSTRAP') or die('Access denied');

/** @var Tygh\Addons\Gdpr\Service $service Gdpr service */
$service = Tygh::$app['addons.gdpr.service'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'export_to_xml') {

        if (!empty($_REQUEST['user_id'])) {
            $user_id = (int) $_REQUEST['user_id'];

            $user_data_collector = Tygh::$app['gdpr.user_data_collector'];
            $user_info = fn_get_user_info($user_id, false);
            $user_data = $user_data_collector->collect($user_info);

            /** @var IDataExtractor $user_personal_data_extractor */
            $user_personal_data_extractor = Tygh::$app['addons.gdpr.user_personal_data_extractor'];
            $user_data = $user_personal_data_extractor->extract($user_data);

            $xml = new DOMDocument('1.0');
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput = true;

            $raw_xml = fn_array_to_xml(array('personal_data' => $user_data));
            $xml->loadXML($raw_xml);
            $formatted_xml = $xml->saveXML();

            /** @var \Tygh\Backend\Storage\File $storage */
            $storage = Storage::instance('custom_files');
            $params = array(
                'contents'  => $formatted_xml,
                'overwrite' => true,
            );

            $file_name = 'personal_data.xml';
            $storage->put($file_name, $params);

            fn_get_file($storage->getAbsolutePath($file_name), '', true);
            exit;
        }
    }

    return array(CONTROLLER_STATUS_OK);
}

if ($mode == 'get_user_data') {
    $user_id = !empty($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;
    $anonymized = false;

    if ($user_id) {
        $user_data_collector = Tygh::$app['gdpr.user_data_collector'];
        $user_info = fn_get_user_info($user_id, false);
        $user_data = $user_data_collector->collect($user_info);

        /** @var IDataExtractor $user_personal_data_extractor */
        $user_personal_data_extractor = Tygh::$app['addons.gdpr.user_extra_personal_data_extractor'];
        $user_personal_data = $user_personal_data_extractor->extract($user_data);
        $anonymized = $service->isUserAnonymized($user_id);
    }


    Tygh::$app['view']->assign(array(
        'gdpr_user_data' => isset($user_personal_data) ? $user_personal_data : array(),
        'anonymized'     => $anonymized,
        'user_id'        => $user_id,
    ));
}

