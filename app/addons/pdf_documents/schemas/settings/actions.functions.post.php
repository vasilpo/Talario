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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Shows warning notification after add-on status changed
 *
 * @param string $new_value New values of pdf_documents setting
 *
 * @return void
 */
function fn_settings_actions_addons_pdf_documents($new_value)
{
    if ($new_value === ObjectStatuses::ACTIVE) {
        fn_set_notification(
            NotificationSeverity::WARNING,
            __('warning'),
            __(
                'pdf_documents.activate_notification',
                [
                    '[service_url]' => Registry::get('addons.pdf_documents.service_url'),
                ]
            ),
            '',
            'pdf_documents_activated'
        );
    } else {
        fn_set_notification(
            NotificationSeverity::WARNING,
            __('warning'),
            __(
                'pdf_documents.disable_notification',
                [
                    '[service_url]'  => Registry::get('addons.pdf_documents.service_url'),
                    '[helpdesk_url]' => Registry::get('config.resources.helpdesk_url'),
                ]
            ),
            '',
            'pdf_documents_disabled'
        );
    }
}
