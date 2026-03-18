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

namespace Tygh\Addons\Retailcrm\Ym\Offers;

use Tygh\Ym\Offers\Apparel as BaseApparel;

/**
 * Class Apparel
 *
 * @package Tygh\Addons\Retailcrm\Ym\Offers
 */
class Apparel extends BaseApparel
{
    /**
     * @inheritdoc
     */
    protected function getApparelOffer($product)
    {
        $this->schema[] = 'purchasePrice';
        if (!in_array('name', $this->schema)) {
            $this->schema[] = 'name';
        }

        $this->offer['attr'] = array_merge($this->offer['attr'], Simple::getRetailCrmOfferAttributes($product));
        $this->offer['items'] = Simple::getRetailCrmOfferItem($this->offer['items'], $product);
    }

    /**
     * @inheritdoc
     */
    protected function buildOfferCombination($product, $combination)
    {
        $result = parent::buildOfferCombination($product, $combination);

        if ($result) {
            $this->offer['attr'] = array_merge($this->offer['attr'], Simple::getRetailCrmOfferAttributes($product, $combination));
            $this->offer['items']['name'] = ApparelSimple::getProductCombinationName($product, $combination);
        }

        return $result;
    }
}
