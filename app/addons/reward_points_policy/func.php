<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Returns reward points policy settings with backward-compatible fallback.
 *
 * @return array<string, mixed>
 */
function fn_reward_points_policy_get_settings(): array
{
    $settings = (array) Registry::get('addons.reward_points_policy');

    return $settings;
}

/**
 * Hook handler for `update_profile`.
 *
 * Adds welcome bonus points for new customer profiles.
 *
 * @param string $action            Profile action
 * @param array  $user_data         Saved user data
 * @param array  $current_user_data Previous user data
 *
 * @return void
 *
 * @see fn_update_user()
 */
function fn_reward_points_policy_update_profile($action, $user_data, $current_user_data): void
{
    if ($action !== 'add' || Registry::get('addons.reward_points.status') !== ObjectStatuses::ACTIVE) {
        return;
    }

    $settings = fn_reward_points_policy_get_settings();
    if (empty($settings['welcome_bonus_enabled']) || $settings['welcome_bonus_enabled'] !== YesNo::YES) {
        return;
    }

    $amount = isset($settings['welcome_bonus_amount']) ? (int) $settings['welcome_bonus_amount'] : 0;
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

    fn_change_user_points(
        $amount,
        $user_id,
        __('reward_points_policy.welcome_bonus_reason'),
        CHANGE_DUE_ADDITION
    );
}

/**
 * Hook handler for `set_point_payment`.
 *
 * Limits payment by reward points according to add-on settings.
 *
 * @param array $cart                           Cart data
 * @param array $cart_products                  Cart products
 * @param array $auth                           Authorization data
 * @param array $user_info                      User information
 * @param float $cost_covered_by_applied_points Covered amount
 * @param float $point_exchange_rate            Exchange rate
 * @param float $user_points                    Available points
 *
 * @return void
 *
 * @see fn_set_point_payment()
 */
function fn_reward_points_policy_set_point_payment(
    &$cart,
    &$cart_products,
    &$auth,
    &$user_info,
    &$cost_covered_by_applied_points,
    &$point_exchange_rate,
    &$user_points
): void {
    $settings = fn_reward_points_policy_get_settings();
    $max_percent = isset($settings['max_points_percent']) ? (float) $settings['max_points_percent'] : 0.0;

    if ($max_percent <= 0 || empty($cart['points_info']['in_use']['points']) || $point_exchange_rate <= 0) {
        return;
    }

    $base_total = isset($cart['subtotal']) ? (float) $cart['subtotal'] : 0.0;
    $base_total += !empty($cart['shipping_cost']) ? (float) $cart['shipping_cost'] : 0.0;
    $base_total += !empty($cart['tax_subtotal']) ? (float) $cart['tax_subtotal'] : 0.0;

    if (!empty($cart['subtotal_discount'])) {
        $discount_without_points = (float) $cart['subtotal_discount'] - (float) $cost_covered_by_applied_points;
        if ($discount_without_points > 0) {
            $base_total -= $discount_without_points;
        }
    }

    if ($base_total <= 0) {
        return;
    }

    $max_points = (int) floor(($base_total * ($max_percent / 100)) * $point_exchange_rate);
    $points_in_use = (int) $cart['points_info']['in_use']['points'];

    if ($points_in_use > $max_points) {
        fn_set_notification(
            'E',
            __('error'),
            __('reward_points_policy.reward_points_limit_exceeded', ['[points]' => $max_points])
        );
        unset($cart['points_info']['in_use']);
    }
}
