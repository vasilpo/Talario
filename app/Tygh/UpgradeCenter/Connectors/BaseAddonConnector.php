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
use Tygh\Addons\AXmlScheme;
use Tygh\Addons\SchemesManager;

/**
 * Base core addon upgrade connector class
 */
abstract class BaseAddonConnector extends BaseConnector
{
    /** @var string */
    protected $addon_id;

    /** @inheritdoc */
    public function onSuccessPackageInstall($content_schema, $information_schema)
    {
        parent::onSuccessPackageInstall($content_schema, $information_schema);

        if ($this->addon_id) {
            SchemesManager::clearInternalCache($this->addon_id);
            /** @var AXmlScheme $scheme */
            $scheme = SchemesManager::getScheme($this->addon_id);

            if ($scheme) {
                $version = $scheme->getVersion();

                if (!empty($version)) {
                    fn_update_addon_version($this->addon_id, $version);
                }
            }
        }
    }

    /** @inheritdoc */
    public function processServerResponse($response, $show_upgrade_notice)
    {
        $data = parent::processServerResponse($response, $show_upgrade_notice);

        if (!empty($data)) {
            $data['name'] = $this->product_name . ': ' . $data['name'];
        }

        return $data;
    }
}