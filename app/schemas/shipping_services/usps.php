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

return [
    'settings' => [
        'intl_mail_classes' => [
            'FIRST-CLASS_PACKAGE_INTERNATIONAL_SERVICE',
            'PRIORITY_MAIL_INTERNATIONAL',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL'
        ],
        'processing_categories' => [
            'CARDS', 'LETTERS', 'FLATS', 'MACHINABLE', 'NONSTANDARD', 'CATALOGS', 'OPEN_AND_DISTRIBUTE',
            'RETURNS', 'SOFT_PACK_MACHINABLE', 'SOFT_PACK_NON_MACHINABLE'
        ],
        'domestic_rate_indicators' => [
            '3D', '3N', '3R', '5D', 'BA', 'BB', 'BM', 'C1', 'C2', 'C3', 'C4', 'C5', 'CP', 'CM', 'DC',
            'DE', 'DF', 'DN', 'DR', 'E4', 'E6', 'E7', 'FA', 'FB', 'FE', 'FP', 'FS', 'LC', 'LF', 'LL',
            'LO', 'LS', 'NP', 'O1', 'O2', 'O3', 'O4', 'O5', 'O6', 'O7', 'OS', 'P5', 'P6', 'P7', 'P8',
            'P9', 'Q6', 'Q7', 'Q8', 'Q9', 'Q0', 'PA', 'PL', 'PM', 'PR', 'SB', 'SN', 'SP', 'SR'
        ],
        'intl_rate_indicators' => [
            'E4', 'E6', 'E7', 'FA', 'FB', 'FE', 'FP', 'FS', 'PA', 'PL', 'SP',
            'EP', 'HA', 'HB', 'HE', 'HL', 'HP', 'HS', 'LE'
        ],
        'domestic_destination_entry_facility_types' => [
            'NONE', 'DESTINATION_NETWORK_DISTRIBUTION_CENTER', 'DESTINATION_SECTIONAL_CENTER_FACILITY',
            'DESTINATION_DELIVERY_UNIT', 'DESTINATION_SERVICE_HUB'
        ],
        'intl_destination_entry_facility_types' => [
            'NONE',
            'INTERNATIONAL_SERVICE_CENTER'
        ],
        'domestic_extra_services' => [
            365, 415, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 810, 811, 812, 813, 814, 815,
            816, 817, 818, 819, 820, 821, 822, 823, 824, 825, 826, 827, 828, 829, 830, 831, 853, 856,
            857, 858, 910, 911, 912, 913, 915, 917, 920, 921, 922, 923, 924, 925, 930, 931, 934, 940,
            941, 955, 957, 972, 981, 984, 986, 991
        ],
        'intl_extra_services' => [
            813, 820, 826, 857, 930, 931, 955
        ],
        'price_types' => [
            'COMMERCIAL',
            'RETAIL'
        ]
    ]
];
