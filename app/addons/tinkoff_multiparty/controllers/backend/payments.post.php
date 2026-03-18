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

use Tygh\Enum\TaxSystems;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * @var string $mode
 */
if (
    $mode === 'processor'
    && (!empty($_REQUEST['processor_id']) || !empty($_REQUEST['payment_id']))
) {
    $processor_data = (!empty($_REQUEST['processor_id']))
        ? db_get_row('SELECT * FROM ?:payment_processors WHERE processor_id = ?i', $_REQUEST['processor_id'])
        : fn_get_processor_data($_REQUEST['payment_id']);

    if (
        !empty($processor_data['processor_script'])
        && $processor_data['processor_script'] === 'tinkoff_multiparty.php'
    ) {
        $tax_systems = TaxSystems::getAllValues();

        /** @var \Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        $view->assign('tax_systems', $tax_systems);
    }
}
