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

use Tygh\Providers\StorefrontProvider;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'list') {
    $promotions = Tygh::$app['view']->getTemplateVars('promotions');
    $storefront = StorefrontProvider::getStorefront();
    $company_ids = $storefront->getCompanyIds();

    if (!empty($company_ids)) {
        $promotions = array_filter((array) $promotions, static function ($promotion) use ($company_ids) {
            /** @var array $promotion */
            return empty($promotion['company_id']) || in_array($promotion['company_id'], $company_ids);
        });
    }

    Tygh::$app['view']->assign('promotions', $promotions);
}
