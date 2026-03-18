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

$schema = [
    TaxType::NONE    => [
        'name' => __('rus_taxes.tax.none'),
    ],
    TaxType::VAT_0   => [
        'name' => __('rus_taxes.tax.vat0'),
    ],
    TaxType::VAT_5   => [
        'name' => __('rus_taxes.tax.vat5'),
    ],
    TaxType::VAT_7   => [
        'name' => __('rus_taxes.tax.vat7'),
    ],
    TaxType::VAT_10  => [
        'name' => __('rus_taxes.tax.vat10'),
    ],
    TaxType::VAT_18  => [
        'name'      => __('rus_taxes.tax.vat18'),
        'is_legacy' => true,
    ],
    TaxType::VAT_20  => [
        'name' => __('rus_taxes.tax.vat20'),
    ],
    TaxType::VAT_22  => [
        'name' => __('rus_taxes.tax.vat22'),
    ],
    TaxType::VAT_105 => [
        'name' => __('rus_taxes.tax.vat105'),
    ],
    TaxType::VAT_107 => [
        'name' => __('rus_taxes.tax.vat107'),
    ],
    TaxType::VAT_110 => [
        'name' => __('rus_taxes.tax.vat110'),
    ],
    TaxType::VAT_118 => [
        'name'      => __('rus_taxes.tax.vat118'),
        'is_legacy' => true,
    ],
    TaxType::VAT_120 => [
        'name' => __('rus_taxes.tax.vat120'),
    ],
    TaxType::VAT_122 => [
        'name' => __('rus_taxes.tax.vat122'),
    ],
];

return $schema;
