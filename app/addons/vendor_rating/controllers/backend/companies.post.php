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

use Tygh\Addons\VendorRating\ServiceProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'update') {
    if (!fn_get_runtime_company_id()) {
        $tabs = Registry::ifGet('navigation.tabs', []);

        $tabs['rating'] = [
            'title' => __('vendor_rating.rating'),
            'js'    => true,
        ];

        Registry::set('navigation.tabs', $tabs);

        $schema = ServiceProvider::getCriteriaSchema();
        if (!empty($schema['manual_vendor_rating'])) {
            /** @var \Tygh\SmartyEngine\Core $view */
            $view = Tygh::$app['view'];
            $view->assign('manual_rating_criterion', $schema['manual_vendor_rating']);
        }
    }
}

return [CONTROLLER_STATUS_OK];

