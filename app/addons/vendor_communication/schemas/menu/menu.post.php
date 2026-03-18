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

use Tygh\Enum\Addons\VendorCommunication\CommunicationTypes;
use Tygh\Registry;
use Tygh\Enum\YesNo;

if (fn_vendor_communication_is_communication_type_active(CommunicationTypes::VENDOR_TO_CUSTOMER)) {
    $schema['central']['customers']['items']['vendor_communication.message_center_name'] = [
        'attrs' => [
            'class' => 'is-addon'
        ],
        'href' => 'vendor_communication.threads?communication_type=' . CommunicationTypes::VENDOR_TO_CUSTOMER,
        'alt' => 'vendor_communication.threads?communication_type=' . CommunicationTypes::VENDOR_TO_CUSTOMER,
        'position' => 900,
    ];
}

return $schema;
