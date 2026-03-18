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

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode === 'bulk_print_pdf' && !empty($_REQUEST['order_ids'])) {
    echo(fn_print_order_invoices($_REQUEST['order_ids'], ['pdf' => true]));

    return [CONTROLLER_STATUS_NO_CONTENT];
}

if ($mode === 'packing_slip_pdf' && !empty($_REQUEST['order_ids'])) {
    echo(fn_print_order_packing_slips($_REQUEST['order_ids'], ['pdf' => true]));

    return [CONTROLLER_STATUS_NO_CONTENT];
}
