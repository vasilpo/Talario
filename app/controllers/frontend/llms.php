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
use Tygh\Licensing\Features;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'view') {
    $llms = new Llms();
    $storefront = Tygh::$app['storefront'];

    $content = $llms->getLlmsTxtContent();

    if (!isset($content) && fn_is_allowed(Features::LLMS)) {
        $llms_data = $llms->getLlmsDataByStorefrontId($storefront->storefront_id);
        $content = $llms_data['data'] ?? null;
    }

    if ($content === null || trim($content) === '') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    header('Content-type: text/plain; charset=utf-8');
    echo($content);
    exit;
}
