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

use Tygh\Models\Company;

if (!fn_allowed_for('MULTIVENDOR')) {
    return;
}

$company = Company::current();
if (!$company) {
    return;
}

$return_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.manage';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode === 'clone' || $mode === 'm_clone' || $mode === 'sell_master_product') {
        if (!$company->canAddProduct(true)) {
            return array(CONTROLLER_STATUS_REDIRECT, $return_url);
        }
    }

    return;
}

if ($mode === 'add') {
    if (!$company->canAddProduct(true)) {
        return array(CONTROLLER_STATUS_REDIRECT, $return_url);
    }
}
