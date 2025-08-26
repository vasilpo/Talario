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

namespace Tygh\Addons\RusSdek2;

use Tygh\Addons\InstallerInterface;
use Tygh\Core\ApplicationInterface;
use Tygh\Languages\Languages;
use Tygh\Registry;

/**
 * This class describes the instructions for installing and uninstalling the rus_sdek2 add-on
 *
 * @package Tygh\Addons\RusSdek2
 */
class Installer implements InstallerInterface
{
    /**
     * @inheritDoc
     */
    public static function factory(ApplicationInterface $app)
    {
        return new self();
    }

    /**
     * @inheritDoc
     */
    public function onBeforeInstall()
    {
    }

    /**
     * @inheritDoc
     */
    public function onInstall()
    {
        $this->installRusSdek2ShippingService();
    }

    /**
     * @inheritDoc
     */
    public function onUninstall()
    {
        $this->uninstallRusSdek2ShippingService();
    }

    /**
     * Actions during add-on installation:
     *  - Adds SDEK shipping service into database.
     *  - Adds cities into table from CSV file.
     *
     * @return void
     */
    private function installRusSdek2ShippingService()
    {
        $service = [
            'status' => 'A',
            'module' => 'sdek2',
            'code' => 'sdek2',
            'sp_file' => '',
            'description' => 'СДЭК',
        ];

        $service['service_id'] = db_replace_into('shipping_services', $service);

        foreach (array_keys(Languages::getAll()) as $service['lang_code']) {
            db_replace_into('shipping_service_descriptions', $service);
        }

        $path = Registry::get('config.dir.root') . '/app/addons/rus_sdek2/database/cities_sdek.csv';
        fn_rus_cities_read_cities_by_chunk($path, RUS_CITIES_FILE_READ_CHUNK_SIZE, __NAMESPACE__ . '\Installer::addCitiesInTable');
    }

    /**
     * Actions during add-on uninstallation:
     *  - Removes SDEK shipping service info from database.
     *
     * @return void
     */
    private function uninstallRusSdek2ShippingService()
    {
        $service_ids = db_get_fields('SELECT service_id FROM ?:shipping_services WHERE module = ?s', 'sdek2');
        db_query('DELETE FROM ?:shipping_services WHERE service_id IN (?a)', $service_ids);
        db_query('DELETE FROM ?:shipping_service_descriptions WHERE service_id IN (?a)', $service_ids);
    }

    /**
     * Adds cities into SDEK cities table.
     *
     * @param array $rows Rows from cities file
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public static function addCitiesInTable(array $rows)
    {
        $cities_hash = fn_rus_cities_get_all_cities($rows);

        foreach ($rows as $city_data) {
            $city_data['City'] = trim($city_data['City']);
            $city_data['City'] = fn_strtolower($city_data['City']);

            if (empty($cities_hash[$city_data['Country']][$city_data['OblName']][$city_data['City']])) {
                continue;
            }

            $city_id = $cities_hash[$city_data['Country']][$city_data['OblName']][$city_data['City']];

            $zipcode = $city_data['PostCodeList'];
            if (!empty($city_data['PostCodeList']) && fn_strlen($city_data['PostCodeList']) <= 1) {
                $zipcode = str_pad($city_data['PostCodeList'], 6, '0', STR_PAD_LEFT);
            }

            $city = [
                'city_id' => $city_id,
                'sdek_city_code' => $city_data['ID'],
                'zipcode' => $zipcode
            ];

            db_replace_into('rus_sdek2_cities_link', $city);
        }
    }
}
