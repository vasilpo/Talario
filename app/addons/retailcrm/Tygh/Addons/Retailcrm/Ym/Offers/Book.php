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

use Tygh\Ym\Offers\Book as BaseBook;

/**
 * Class Book
 *
 * @package Tygh\Addons\Retailcrm\Ym\Offers
 */
class Book extends BaseBook
{
    /**
     * @inheritdoc
     */
    public function gatherAdditional($product)
    {
        parent::gatherAdditional($product);

        $this->schema[] = 'purchasePrice';

        $this->offer['attr'] = array_merge($this->offer['attr'], Simple::getRetailCrmOfferAttributes($product));
        $this->offer['items'] = Simple::getRetailCrmOfferItem($this->offer['items'], $product);

        return true;
    }
}
