<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\RusSdek2\HookHandlers;

use Tygh\Application;
use Tygh\Template\Document\Variables\PickpupPointVariable;

/**
 * This class describes the hook handlers related to pickup point variable
 *
 * @package Tygh\Addons\RusSdek2\HookHandlers
 */
class PickupPointsHookHandler
{
    /** @var Application */
    protected $application;

    /**
     * @param Application $application Application object
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * The `pickup_point_variable_init` hook handler.
     *
     * Action performed:
     *     - Sets pickup point data.
     *
     * @param \Tygh\Template\Document\Variables\PickpupPointVariable $instance        Variable instance
     * @param array                                                  $order           Order data
     * @param string                                                 $lang_code       Two-letter language code
     * @param bool                                                   $is_selected     Whether a pickup point is
     *                                                                                selected as the shipping
     *                                                                                destination point
     * @param string                                                 $name            Pickup point name
     * @param string                                                 $phone           Pickup point phone
     * @param string                                                 $full_address    Pickup point full address
     * @param string[]                                               $open_hours_raw  List of open hours
     * @param string                                                 $open_hours      Formatted open hours
     * @param string                                                 $description_raw Pickup point description
     * @param string                                                 $description     Formatted pickup point description
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onPickupPointVariableInit(
        PickpupPointVariable $instance,
        array $order,
        $lang_code,
        &$is_selected,
        &$name,
        &$phone,
        &$full_address,
        array &$open_hours_raw,
        &$open_hours,
        &$description_raw,
        &$description
    ) {
        if (empty($order['shipping'])) {
            return;
        }

        if (is_array($order['shipping'])) {
            $shipping = reset($order['shipping']);
        } else {
            $shipping = $order['shipping'];
        }

        if (!isset($shipping['module']) || $shipping['module'] !== 'sdek2') {
            return;
        }

        if (!isset($shipping['office_data'])) {
            return;
        }

        $pickup_data = $shipping['office_data'];

        $is_selected = true;
        $name = $pickup_data['location']['address'];
        $full_address = $pickup_data['location']['address_full'];
        $phone = $pickup_data['phones'][0]['number'] ?? '';
        $open_hours = $pickup_data['work_time'];
        $open_hours_raw = [$open_hours];
        $description_raw = $description = $pickup_data['address_comment'];
    }
}
