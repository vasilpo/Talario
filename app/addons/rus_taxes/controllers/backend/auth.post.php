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

use Tygh\Addons\RusTaxes\TaxType;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $time_x = strtotime('2019-01-01 00:00:00');

    if ($mode === 'login' && TIME >= $time_x) {
        /** @var array $auth */
        $auth = Tygh::$app['session']['auth'];
        if (empty($auth['user_id'])
            || !empty($auth['company_id'])
            || !fn_check_permissions('taxes', 'convert_legacy_taxes', 'admin')
        ) {
            return [CONTROLLER_STATUS_OK];
        }

        /** @var \Tygh\Database\Connection $db */
        $db = Tygh::$app['db'];
        $taxes = $db->getColumn('SELECT * FROM ?:taxes WHERE tax_type IN (?a)', [TaxType::VAT_18, TaxType::VAT_118]);

        if ($taxes) {
            fn_set_notification(
                'I',
                __('rus_taxes.tax_rates_changes.title'),
                __('rus_taxes.tax_rates_changes.warning', [
                    '[product]'     => PRODUCT_NAME,
                    '[law_url]'     => 'https://normativ.kontur.ru/document?moduleId=1&documentId=318259&promocode=0957',
                    '[convert_url]' => fn_url('taxes.convert_legacy_taxes'),
                ]),
                'K'
            );
        }
    }
}
