<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
