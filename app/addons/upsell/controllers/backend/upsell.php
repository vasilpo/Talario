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
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Http;
use Tygh\Providers\LicensingProvider;
use Tygh\Registry;
use Tygh\Settings;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        $mode === 'send_request'
        && !empty($_REQUEST['feature'])
    ) {
        $feature = $_REQUEST['feature'];
        $from_dispatch = $_REQUEST['from_dispatch'] ?? '';
        $user_info = Registry::get('user_info');
        $email = $user_info['email'];
        $storage_key = 'upsell.' . $feature;

        if (fn_is_expired_storage_data($storage_key, SECONDS_IN_DAY * 60)) {
            $uc_settings = Settings::instance()->getValues('Upgrade_center');
            $license_number = $uc_settings['license_number'];
            $current_plan = LicensingProvider::getLicensingService()->getCurrentPlan();

            if ($license_number) {
                $request_data = [
                    'license_number' => $license_number,
                    'feature'        => $feature,
                    'from_dispatch'  => $from_dispatch,
                    'email'          => $email,
                    'current_plan'   => $current_plan->getKey(),
                    'domain'         => str_replace(fn_get_index_script(UserTypes::ADMIN), '', fn_url('', SiteArea::ADMIN_PANEL))
                ];

                $logging = Http::$logging;
                Http::$logging = false;

                Http::post(
                    Registry::get('config.resources.updates_server') . '/index.php?dispatch=upsell.request',
                    $request_data,
                    ['timeout' => 10]
                );

                Http::$logging = $logging;

                if (Http::getStatus() === 201) {
                    fn_set_notification(
                        NotificationSeverity::INFO,
                        __('upsell.message_sent'),
                        '<p>' . __('upsell.message_sent_success') . '</p>'
                        . '<p class="pull-right"><button class="cm-notification-close btn btn-primary">' . __('ok') . '</button></p>'
                    );
                } elseif (Http::getStatus() === 409) {
                    fn_set_notification(NotificationSeverity::INFO, __('upsell.message_already_sent'), __('upsell.message_already_sent.notice'));
                } else {
                    fn_set_storage_data($storage_key);
                    fn_set_notification(NotificationSeverity::INFO, __('upsell.message_couldnt_send'), __('upsell.message_couldnt_send.notice'));
                }
            }
        } else {
            fn_set_notification(NotificationSeverity::INFO, __('upsell.message_already_sent'), __('upsell.message_already_sent.notice'));
        }
    }

    return [CONTROLLER_STATUS_OK];
}
