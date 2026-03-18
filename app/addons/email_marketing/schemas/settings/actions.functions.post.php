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

use Tygh\Registry;
use Tygh\EmailSync;

function fn_settings_variants_addons_email_marketing_em_mailchimp_list()
{
    if (Registry::get('addons.email_marketing.status') == 'A' && Registry::get('addons.email_marketing.em_mailchimp_api_key')) {
        $list = array('' => __('none'));
        $list = array_merge($list, EmailSync::instance('mailchimp')->getLists());

    } else {
        $list = array(
            '' => __('email_marketing.enter_api_key_and_save')
        );
    }

    return $list;
}

function fn_settings_actions_addons_email_marketing_em_mailchimp_list(&$new_value, $old_value)
{
    if (Registry::get('addons.email_marketing.status') != 'A') {
        return false;
    }

    // resubscribe web hooks
    if ($new_value != $old_value) {
        $result = true;
        if (!empty($old_value)) {
            $result = EmailSync::instance('mailchimp')->unsubscribeCallback($old_value);
        }

        if (!empty($new_value) && $result) {
            $result = EmailSync::instance('mailchimp')->subscribeCallback($new_value);
        }

        if ($result == false) {
            $new_value = $old_value;
        }
    }
}
