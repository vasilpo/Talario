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

namespace Tygh\Ym\Offers;

use Tygh\Ym\Logs;

class Vendor extends Base
{
    protected $offer_type = 'vendor';

    protected $schema = array(
        'url',
        'price',
        'oldprice',
        'currencyId',
        'categoryId',
        'picture',
        'store',
        'pickup',
        'delivery',
        'delivery-options',
        'typePrefix',
        'vendor',
        'vendorCode',
        'model',
        'description',
        'sales_notes',
        'manufacturer_warranty',
        'country_of_origin',
        'adult',
        'age',
        'barcode',
        'cpa',
        'rec',
        'expiry',
        'weight',
        'dimensions',
        'downloadable',
        'purchase_price',
        'param',
    );

    public function postBuild($xml, $product, $offer_data)
    {
        if (empty($offer_data['items']['vendor'])) {
            $this->log->write(Logs::SKIP_PRODUCT, $product, __('yml2_log_brand_is_empty'));
            return false;
        }

        return true;
    }

    public function gatherAdditionalExt($product)
    {
        $this->offer['attr']['type'] = "vendor.model";

        return true;
    }
}
