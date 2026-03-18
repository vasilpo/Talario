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

use Tygh\Common\Llms;
use Tygh\Enum\NotificationSeverity;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect_params = [];
    $llms = new Llms();

    if ($mode === 'update') {
        if (!empty($_REQUEST['llms_data']) && isset($_REQUEST['llms_data']['content'])) {
            if (fn_allowed_for('ULTIMATE') && isset($_REQUEST['llms_data']['update_content'])) {
                /** @var \Tygh\Storefront\repository $repository */
                $repository = Tygh::$app['storefront.repository'];
                list($all_storefronts,) = $repository->find();
                foreach ($all_storefronts as $storefront) {
                    $llms->setLlmsDataForStorefrontId($storefront->storefront_id, $_REQUEST['llms_data']['content']);
                }
            } else {
                /** @var \Tygh\Storefront\Storefront $storefront */
                $storefront = Tygh::$app['storefront'];
                $storefront_id = $storefront->storefront_id;

                $llms->setLlmsDataForStorefrontId($storefront_id, $_REQUEST['llms_data']['content']);
            }
        }
    }

    return [CONTROLLER_STATUS_OK, 'llms.manage?' . http_build_query($redirect_params)];
}

if ($mode === 'manage') {
    $storefront = Tygh::$app['storefront'];
    $storefront_id = $storefront->storefront_id;

    $llms = new Llms();
    $llms_data = $llms->getLlmsDataByStorefrontId($storefront_id);

    $content = $llms_data['data'] ?? '';

    $llms_file_content = $llms->getLlmsTxtContent();
    if ($llms_file_content !== null) {
        fn_set_notification(NotificationSeverity::WARNING, __('notice'), __('information_file_llms'));
    }

    Tygh::$app['view']->assign('llms', $content);
}
