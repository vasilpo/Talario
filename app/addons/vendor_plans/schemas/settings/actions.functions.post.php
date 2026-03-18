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

use Tygh\Enum\ProfileTypes;

/**
 * Uncheck vendor_plan profile field selection
 */
function fn_settings_actions_addons_vendor_plans($new_status, $old_status, $on_install)
{
    if ($new_status == 'D') {
        $field_data = array(
            'profile_show'      => 'N',
            'profile_required'  => 'N',
            'checkout_show'     => 'N',
            'checkout_required' => 'N',
            'partner_show'      => 'N',
            'partner_required'  => 'N',
        );

        $plan_field_id = (int) db_get_field('SELECT field_id FROM ?:profile_fields WHERE profile_type = ?s AND field_name = ?s',
            ProfileTypes::CODE_SELLER,
            'plan_id'
        );

        if ($plan_field_id) {
            fn_update_profile_field($field_data, $plan_field_id);
        }
    }

    return true;
}