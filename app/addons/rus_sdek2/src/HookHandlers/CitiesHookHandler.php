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

/**
 * This class describes the hook handlers related to cities
 *
 * @package Tygh\Addons\RusSdek2\HookHandlers
 */
class CitiesHookHandler
{
    /**
     * The `get_cities_post` hook handler.
     *
     * @param array  $params         Extra parameters
     * @param int    $items_per_page Items per page
     * @param string $lang_code      Language code
     * @param array  $cities         Cities data
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onGetCitiesPost(array $params, $items_per_page, $lang_code, array &$cities)
    {
        $cities_ids = array_column($cities, 'city_id');

        if ($cities_ids) {
            $sdek_ids = db_get_hash_multi_array(
                'SELECT city_id, sdek_city_code FROM ?:rus_sdek2_cities_link WHERE city_id IN (?a)',
                ['city_id', 'sdek_city_code', 'sdek_city_code'],
                $cities_ids
            );
        }

        if (!isset($sdek_ids)) {
            return;
        }

        foreach ($cities as &$city_data) {
            $city_data['sdek_city_code'] = empty($sdek_ids[$city_data['city_id']])
                ? ''
                : implode(',', $sdek_ids[$city_data['city_id']]);
        }
    }

    /**
     * The `update_city_post` hook handler.
     *
     * @param array $city_data City data
     * @param int   $city_id   City identifier
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onUpdateCityPost(array $city_data, $city_id)
    {
        if (
            empty($city_data['sdek_city_code'])
            || (
                !empty($city_data['sdek_city_code_old'])
                && $city_data['sdek_city_code_old'] === $city_data['sdek_city_code']
            )
        ) {
            return;
        }

        $sdek_city_code = explode(',', $city_data['sdek_city_code']);

        if (count($sdek_city_code) > 1) {
            foreach ($sdek_city_code as $sdek_code) {
                $sdek_link = [
                    'city_id' => $city_id,
                    'sdek_city_code' => $sdek_code,
                    'zipcode' => $city_data['sdek_city_zipcode']
                ];

                db_replace_into('rus_sdek2_cities_link', $sdek_link);
            }
        } else {
            $sdek_link = [
                'city_id' => $city_id,
                'sdek_city_code' => $city_data['sdek_city_code'],
                'zipcode' => $city_data['sdek_city_zipcode']
            ];

            db_replace_into('rus_sdek2_cities_link', $sdek_link);
        }
    }

    /**
     * The `delete_city_post` hook handler.
     *  - Executes before cities deletion.
     *  - Deletes cities in SDEK table.
     *
     * @param int $city_id City identifier
     *
     * @return void
     */
    public function onDeleteCityPost($city_id)
    {
        db_query('DELETE FROM ?:rus_sdek2_cities_link WHERE city_id = ?i', $city_id);
    }
}
