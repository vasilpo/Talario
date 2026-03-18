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

use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Disables product bundle-based promo actions upon add-on disable.
 *
 * @param string $new_status New add-on status
 * @param string $old_status Old add-on status
 * @param bool   $on_install Whether action is performed during add-on installation
 *
 * @return void
 *
 * @internal
 */
function fn_settings_actions_addons_product_bundles($new_status, $old_status, $on_install)
{
    if ($new_status === ObjectStatuses::DISABLED) {
        $promotions_data = db_get_array('SELECT p.* FROM ?:promotions AS p INNER JOIN ?:product_bundles AS pb ON pb.linked_promotion_id = p.promotion_id');
        if (!$promotions_data) {
            return;
        }
        fn_set_storage_data('product_bundles_stored_promotions', json_encode($promotions_data));
        db_query('DELETE FROM ?:promotions WHERE promotion_id IN (?n)', array_column($promotions_data, 'promotion_id'));
    } elseif ($new_status === ObjectStatuses::ACTIVE) {
        $stored_promotions = fn_get_storage_data('product_bundles_stored_promotions');
        if (!$stored_promotions) {
            return;
        }

        $stored_promotions = json_decode($stored_promotions, true);
        db_replace_into('promotions', $stored_promotions, true);

        fn_set_storage_data('product_bundles_stored_promotions', '');
    }
}
