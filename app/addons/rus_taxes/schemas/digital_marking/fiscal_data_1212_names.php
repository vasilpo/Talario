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

use Tygh\Enum\FiscalData1212Objects;

defined('BOOTSTRAP') or die('Access denied');

return [
    FiscalData1212Objects::COMMODITY                   => 'commodity',
    FiscalData1212Objects::EXCISE                      => 'excise',
    FiscalData1212Objects::JOB                         => 'job',
    FiscalData1212Objects::SERVICE                     => 'service',
    FiscalData1212Objects::PAYMENT                     => 'payment',
    FiscalData1212Objects::WITH_MARKING_CODE           => 'product_with_marking_code',
    FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE => 'excisable_product_with_marking_code'
];
