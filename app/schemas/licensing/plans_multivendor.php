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

use Tygh\Enum\YesNo as YesNoEnum;
use Tygh\Licensing\Features;
use Tygh\Licensing\Plan;
use Tygh\Licensing\Rules\CustomRule;
use Tygh\Licensing\Rules\YesNoRule;

defined('BOOTSTRAP') or die('Access denied');

return [
    new Plan('default', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::NO),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::NO),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::YES),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::NO),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::NO),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::NO),
        Features::FULL_PAGE_CACHE       => new CustomRule($addon_full_page_cache_handler),
        Features::PWA                   => new YesNoRule(YesNoEnum::YES),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::NO),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, false),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::NO),
        Features::LLMS                  => new YesNoRule(YesNoEnum::NO),
    ]),
    new Plan('trial', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::YES),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::YES),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::YES),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::YES),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::YES),
        Features::FULL_PAGE_CACHE       => new YesNoRule(YesNoEnum::YES),
        Features::PWA                   => new YesNoRule(YesNoEnum::YES),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::YES),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, true),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::YES),
        Features::LLMS                  => new YesNoRule(YesNoEnum::NO),
    ]),
    new Plan('standard', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::NO),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::NO),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::NO),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::NO),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::NO),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::NO),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::NO),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::NO),
        Features::FULL_PAGE_CACHE       => new CustomRule($addon_full_page_cache_handler),
        Features::PWA                   => new YesNoRule(YesNoEnum::NO),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::NO),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, false),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::NO),
        Features::LLMS                  => new YesNoRule(YesNoEnum::NO),
    ]),
    new Plan('plus', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::NO),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::YES),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::YES),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::NO),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::NO),
        Features::FULL_PAGE_CACHE       => new CustomRule($addon_full_page_cache_handler),
        Features::PWA                   => new YesNoRule(YesNoEnum::YES),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::NO),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, false),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::YES),
        Features::LLMS                  => new YesNoRule(YesNoEnum::YES),
    ]),
    new Plan('ultimate', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::YES),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::YES),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::YES),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::YES),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::YES),
        Features::FULL_PAGE_CACHE       => new YesNoRule(YesNoEnum::YES),
        Features::PWA                   => new YesNoRule(YesNoEnum::YES),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::YES),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, true),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::YES),
        Features::LLMS                  => new YesNoRule(YesNoEnum::YES),
    ]),
    new Plan('unlim', [
        Features::WAREHOUSES            => new YesNoRule(YesNoEnum::YES),
        Features::MASTER_PRODUCTS       => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_RATING         => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_CATEGORIES_FEE => new YesNoRule(YesNoEnum::YES),
        Features::VENDOR_PRIVILEGES     => new YesNoRule(YesNoEnum::YES),
        Features::DIRECT_PAYMENTS       => new YesNoRule(YesNoEnum::YES),
        Features::ORDER_FULFILLMENT     => new YesNoRule(YesNoEnum::YES),
        Features::PAYMENTS_BY_COUNTRY   => new YesNoRule(YesNoEnum::YES),
        Features::FULL_PAGE_CACHE       => new YesNoRule(YesNoEnum::YES),
        Features::PWA                   => new YesNoRule(YesNoEnum::YES),
        Features::MULTIPLE_STOREFRONTS  => new YesNoRule(YesNoEnum::YES),
        Features::ADD_STOREFRONT        => new CustomRule($add_storefront_handler, true),
        Features::PRODUCT_VIDEOS        => new YesNoRule(YesNoEnum::YES),
        Features::LLMS                  => new YesNoRule(YesNoEnum::YES),
    ]),
];
