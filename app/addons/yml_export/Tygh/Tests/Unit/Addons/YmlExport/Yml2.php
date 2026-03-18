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

namespace Tygh\Tests\Unit\Addons\YmlExport;

class Yml2 extends \Tygh\Ym\Yml2
{
    protected function getStorageData($key)
    {
        return null;
    }

    protected function getPriceList($price_id)
    {
        return array();
    }

    protected function getOptions($price_id)
    {
        return array();
    }

    public function getFilePath()
    {
        return '';
    }

    public function getTempFilePath()
    {
        return '';
    }

    protected function getYMLCategories($field_name)
    {
        return array();
    }

    protected function formatDate($timestamp)
    {
        $dt = new \DateTime('now', new \DateTimeZone('UTC'));
        $dt->setTimestamp($timestamp);

        return $dt->format('d.m.Y');
    }

    protected function createLogger($format = 'csv', $price_id = 0)
    {
        return null;
    }

    /**
     * Adapter method to call protected method.
     *
     * @param array $product_features_data
     * @param array $features
     *
     * @return array
     */
    public function getProductFeaturesAdapter($product_features_data, $features)
    {
        return $this->getProductFeatures($product_features_data, $features);
    }
}