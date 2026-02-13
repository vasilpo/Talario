<?php

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Enum\UserTypes;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * The `get_product_data_post` hook handler.
 *
 * @param array  $product_data Product data
 * @param array  $auth         Authorization data
 * @param bool   $preview      Preview mode flag
 * @param string $lang_code    Two-letter language code
 *
 * @return void
 */
function fn_exikane_changes_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (empty($product_data['product_id'])) {
        return;
    }

    $site = fn_exikane_changes_get_partner_site((int) $product_data['product_id']);
    if ($site !== '') {
        $product_data['exikane_partner_site'] = $site;
    }
}

/**
 * The `update_profile` hook handler.
 *
 * Adds welcome reward points after a customer profile is created.
 *
 * @param string $action            Profile update action
 * @param array  $user_data         Updated user data
 * @param array  $current_user_data Current user data
 *
 * @return void
 */
function fn_exikane_changes_update_profile($action, $user_data, $current_user_data)
{
    if ($action !== 'add' || Registry::get('addons.reward_points.status') !== ObjectStatuses::ACTIVE) {
        return;
    }

    $addons = Registry::get('addons.exikane_changes');
    if (empty($addons['welcome_bonus_enabled']) || $addons['welcome_bonus_enabled'] !== YesNo::YES) {
        return;
    }

    $amount = isset($addons['welcome_bonus_amount']) ? (int) $addons['welcome_bonus_amount'] : 0;
    if ($amount <= 0) {
        return;
    }

    $user_id = !empty($user_data['user_id'])
        ? (int) $user_data['user_id']
        : (!empty($current_user_data['user_id']) ? (int) $current_user_data['user_id'] : 0);
    if ($user_id <= 0) {
        return;
    }

    $user_type = !empty($user_data['user_type'])
        ? $user_data['user_type']
        : (!empty($current_user_data['user_type']) ? $current_user_data['user_type'] : '');
    if ($user_type && $user_type !== UserTypes::CUSTOMER) {
        return;
    }

    fn_change_user_points($amount, $user_id, __('exikane_changes.welcome_bonus_reason'), CHANGE_DUE_ADDITION);
}

/**
 * The `set_point_payment` hook handler.
 *
 * Limits the maximum reward points that can be applied to the order total.
 *
 * @param array $cart                           Array of cart data.
 * @param array $cart_products                  List of cart products.
 * @param array $auth                           Array of user authentication data (e.g. uid, usergroup_ids, etc.).
 * @param array $user_info                      Array of user data.
 * @param float $cost_covered_by_applied_points Total sum of products covered by previously applied points.
 * @param float $point_exchange_rate            The number of points equal to 1 conventional unit.
 * @param float $user_points                    Total sum of points available for user.
 *
 * @return void
 */
function fn_exikane_changes_set_point_payment(
    &$cart,
    &$cart_products,
    &$auth,
    &$user_info,
    &$cost_covered_by_applied_points,
    &$point_exchange_rate,
    &$user_points
) {
    $addons = Registry::get('addons.exikane_changes');
    $max_percent = isset($addons['max_points_percent']) ? (float) $addons['max_points_percent'] : 0.0;
    if ($max_percent <= 0) {
        return;
    }

    if (empty($cart['points_info']['in_use']['points'])) {
        return;
    }

    if ($point_exchange_rate <= 0) {
        return;
    }

    $base_total = isset($cart['subtotal']) ? (float) $cart['subtotal'] : 0.0;

    if (!empty($cart['shipping_cost'])) {
        $base_total += (float) $cart['shipping_cost'];
    }

    if (!empty($cart['tax_subtotal'])) {
        $base_total += (float) $cart['tax_subtotal'];
    }

    if (!empty($cart['subtotal_discount'])) {
        $discount_without_points = (float) $cart['subtotal_discount'] - (float) $cost_covered_by_applied_points;
        if ($discount_without_points > 0) {
            $base_total -= $discount_without_points;
        }
    }

    if ($base_total <= 0) {
        return;
    }

    $max_cost = $base_total * ($max_percent / 100);
    $max_points = (int) floor($max_cost * $point_exchange_rate);
    $points_in_use = (int) $cart['points_info']['in_use']['points'];

    if ($points_in_use > $max_points) {
        fn_set_notification(
            'E',
            __('error'),
            __('exikane_changes.reward_points_limit_exceeded', ['[points]' => $max_points])
        );
        unset($cart['points_info']['in_use']);
    }
}

/**
 * The `update_product_post` hook handler.
 *
 * @param array  $product_data Product data
 * @param int    $product_id   Product identifier
 * @param string $lang_code    Two-letter language code
 * @param bool   $create       True when product is created
 *
 * @return void
 */
function fn_exikane_changes_update_product_post($product_data, $product_id, $lang_code, $create)
{
    if (!isset($product_data['exikane_partner_site'])) {
        return;
    }

    $site = trim((string) $product_data['exikane_partner_site']);
    if ($site === '') {
        db_query('DELETE FROM ?:exikane_partner_product_sites WHERE product_id = ?i', $product_id);
        return;
    }

    db_query('REPLACE INTO ?:exikane_partner_product_sites ?e', [
        'product_id' => (int) $product_id,
        'site'       => $site
    ]);
}

/**
 * The `delete_product_post` hook handler.
 *
 * @param int  $product_id      Product identifier
 * @param bool $product_deleted Product deletion result
 *
 * @return void
 */
function fn_exikane_changes_delete_product_post($product_id, $product_deleted)
{
    if (!$product_deleted) {
        return;
    }

    db_query('DELETE FROM ?:exikane_partner_product_sites WHERE product_id = ?i', $product_id);
    db_query('DELETE FROM ?:exikane_partner_site_clicks WHERE product_id = ?i', $product_id);
}

/**
 * Gets partner website URL by product identifier.
 *
 * @param int $product_id Product identifier
 *
 * @return string
 */
function fn_exikane_changes_get_partner_site($product_id)
{
    $site = (string) db_get_field(
        'SELECT site FROM ?:exikane_partner_product_sites WHERE product_id = ?i',
        $product_id
    );

    return trim($site);
}

/**
 * Normalizes website URL by prepending protocol when needed.
 *
 * @param string $url Website URL
 *
 * @return string
 */
function fn_exikane_changes_normalize_site_url($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (!preg_match('~^https?://~i', $url)) {
        $url = 'http://' . $url;
    }

    return $url;
}

/**
 * Appends fixed UTM parameters to partner website URL.
 *
 * @param string $url Website URL
 *
 * @return string
 */
function fn_exikane_changes_attach_partner_utm($url)
{
    $utm = 'utm_source=talario&utm_medium=partner&utm_campaign=partner_site';

    return fn_link_attach($url, $utm);
}

/**
 * Logs partner website click.
 *
 * @param int   $product_id Product identifier
 * @param array $auth       Authorization data
 *
 * @return int|string Query result identifier
 */
function fn_exikane_changes_log_partner_click($product_id, array $auth)
{
    $user_id = isset($auth['user_id']) ? (int) $auth['user_id'] : 0;
    $email = '';

    if ($user_id > 0) {
        $email = !empty($auth['email']) ? (string) $auth['email'] : '';
        if ($email === '') {
            $user_data = fn_get_user_info($user_id);
            if (!empty($user_data['email'])) {
                $email = (string) $user_data['email'];
            }
        }
    }

    return db_query('INSERT INTO ?:exikane_partner_site_clicks ?e', [
        'user_id'   => $user_id,
        'email'     => $email,
        'product_id' => (int) $product_id,
        'timestamp' => TIME
    ]);
}
