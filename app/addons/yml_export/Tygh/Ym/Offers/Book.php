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

class Book extends Base
{
    protected $offer_type = 'book';

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
        'author',
        'name',
        'publisher',
        'series',
        'year',
        'ISBN',
        'volume',
        'part',
        'language',
        'binding',
        'page_extent',
        'table_of_contents',
        'description',
        'sales_notes',
        'manufacturer_warranty',
        'barcode',
        'age',
        'adult',
        'cpa',
        'expiry',
        'weight',
        'dimensions',
        'downloadable',
        'purchase_price',
        'country_of_origin',
        'param'
    );

    protected $features = array(
        'author',
        'publisher',
        'series',
        'year',
        'ISBN',
        'volume',
        'part',
        'language',
        'binding',
        'page_extent',
        'table_of_contents'
    );

    public function gatherAdditional($product)
    {
        $this->offer['attr']['type'] = "book";
        $this->offer['items']['name'] = $this->getOfferName($product);

        return true;
    }
}
