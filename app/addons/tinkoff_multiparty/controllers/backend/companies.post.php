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

use Tygh\Addons\TinkoffMultiparty\Enum\AddressesTypes;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (
    $mode === 'update'
    && fn_allowed_for('MULTIVENDOR')
) {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    /** @var array<string, string> $company_data */
    $company_data = $view->getTemplateVars('company_data');
    if (empty($company_data)) {
        return;
    }

    $shop_data = !empty($company_data['tinkoff_multiparty_shop_data'])
        ? unserialize($company_data['tinkoff_multiparty_shop_data'])
        : [];

    $tinkoff_multiparty_shopcode = !empty($company_data['tinkoff_multiparty_shopcode'])
        ? $company_data['tinkoff_multiparty_shopcode']
        : '';

    Registry::set(
        'navigation.tabs.tinkoff_multiparty',
        [
            'title' => __('addons.tinkoff_multiparty.tinkoff_multiparty'),
            'js'    => true,
        ]
    );

    $view->assign([
        'company_data'                => $company_data,
        'addresses_types'             => AddressesTypes::getAllValues(),
        'shop_data'                   => $shop_data,
        'tinkoff_multiparty_shopcode' => $tinkoff_multiparty_shopcode
    ]);
}
