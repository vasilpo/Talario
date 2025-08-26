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

namespace Tygh\Addons\RusSdek2\Services;

use Tygh\Database\Connection;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;

class CitiesService
{
    /** @var Connection */
    protected $db;

    /**
     * @param Connection $db Database
     *
     * @return void
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Gets SDEK city ID.
     *
     * @param array $location Location data
     *
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getCityId(array $location)
    {
        $result = '';
        $country = 1;
        $state = '';
        $country_code = '';

        if (!empty($location['country'])) {
            $country = fn_get_country_states($location['country']);

            if (!empty($country)) {
                $country_code = $location['country'];
            }
        }

        if (empty($country) || empty($location['city'])) {
            return '';
        }

        if (!empty($location['state'])) {
            $state = $this->checkStateCode($location['state'], $country_code) ? $location['state'] : '';
        }

        $data_cities = fn_rus_cities_get_city_ids($location['city'], $state, $country_code);

        if (empty($data_cities)) {
            if (AREA !== SiteArea::STOREFRONT) {
                fn_set_notification(NotificationSeverity::ERROR, __('notice'), __('rus_sdek2.admin_city_not_served'));
            }

            return '';
        } else {
            $data_cities = is_array($data_cities) ? $data_cities : [$data_cities];
            $sdek_data = $this->getSdekData($data_cities);

            if (!empty($sdek_data)) {
                if (count($sdek_data) === 1) {
                    $data = reset($sdek_data);
                    $result = $data['sdek_city_code'];
                } elseif (!empty($location['zipcode'])) {
                    $city_ids = [];

                    foreach ($sdek_data as $data) {
                        $city_ids[$data['city_id']] = $data['city_id'];

                        if (empty($data['zipcode'])) {
                            $sdek_city_code = $data['sdek_city_code'];
                            continue;
                        }

                        $zipcodes = explode(',', $data['zipcode']);

                        if (in_array($location['zipcode'], $zipcodes)) {
                            $result = $data['sdek_city_code'];
                        }
                    }

                    if (empty($result) && count($city_ids) === 1 && !empty($sdek_city_code)) {
                        $result = $sdek_city_code;
                    }
                }
            }

            if (empty($result)) {
                if (AREA !== SiteArea::STOREFRONT) {
                    fn_set_notification(NotificationSeverity::ERROR, __('notice'), __('rus_sdek2.admin_city_select_error'));
                } else {
                    fn_set_notification(NotificationSeverity::ERROR, __('notice'), __('rus_sdek2.city_select_error'));
                }
            }
        }

        return $result;
    }

    /**
     * Checks SDEK state code exists.
     *
     * @param string $state      State code
     * @param string $country    Country code
     * @param bool   $avail_only State
     *
     * @return bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function checkStateCode($state, $country = '', $avail_only = true)
    {
        $condition = '';

        if (!empty($country)) {
            $condition .= $this->db->quote(' AND country_code = ?s', $country);
        }

        if ($avail_only) {
            $condition .= $this->db->quote(' AND status = ?s', 'A');
        }

        $state = $this->db->getField(
            'SELECT code FROM ?:states WHERE code = ?s ?p',
            $state,
            $condition
        );

        if (empty($state)) {
            return false;
        }

        return true;
    }

    /**
     * Gets SDEK cities data.
     *
     * @param array $city_ids City IDs
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getSdekData(array $city_ids)
    {
        return $this->db->getArray(
            'SELECT city_id, sdek_city_code, zipcode FROM ?:rus_sdek2_cities_link WHERE city_id IN (?a)',
            $city_ids
        );
    }
}
