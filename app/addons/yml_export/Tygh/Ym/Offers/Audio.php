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

use Tygh\Ym\Offers;

class Audio extends Base
{
    protected $offer_type = 'audio';

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
        'artist',
        'title',
        'year',
        'media',
        'description',
        'sales_notes',
        'manufacturer_warranty',
        'country_of_origin',
        'adult',
        'age',
        'barcode',
        'cpa',
        'expiry',
        'weight',
        'dimensions',
        'purchase_price',
        'downloadable',
        'country',
        'param'
    );

    protected $features = array(
        'artist',
        'year',
        'media',
    );

    public function gatherAdditional($product)
    {
        $this->offer['attr']['type'] = "artist.title";
        $this->offer['items']['title'] = $this->getOfferName($product);

        return true;
    }
}
