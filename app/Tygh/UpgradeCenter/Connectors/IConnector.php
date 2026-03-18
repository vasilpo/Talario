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

namespace Tygh\UpgradeCenter\Connectors;

/**
 * Upgrade connectors interface
 */
interface IConnector
{
    /**
     * Prepares request data for request to Upgrade server.
     *
     * @return array Prepared request information
     */
    public function getConnectionData();

    /**
     * Processes the response from the Upgrade server. Connector sould:
     *      * Display notice about the new upgrade if available
     *      * Download an XML file with the upgrade pack description
     *
     * @param  string $response            server response
     * @param  bool   $show_upgrade_notice internal flag, that allows/disallows Connector displays upgrade notice (A new version of [product] available)
     * @return array  Upgrade package information or empty array if upgrade is not available
     */
    public function processServerResponse($response, $show_upgrade_notice);

    /**
     * Downloads upgrade package from the Upgade server
     *
     * @param  array  $schema       Package schema
     * @param  string $package_path Path where the upgrade pack must be saved
     * @return bool   True if upgrade package was successfully downloaded, false otherwise
     */
    public function downloadPackage($schema, $package_path);
}
