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


namespace Tygh\Addons\ProductVariations\Product\Group\Events;


use Tygh\Addons\ProductVariations\Product\Group\GroupProduct;

class ProductRemovedEvent extends AEvent
{
    protected $product;

    protected function __construct(GroupProduct $product)
    {
        $this->product = $product;
    }

    public static function create(GroupProduct $product)
    {
        return new self($product);
    }

    /**
     * @return \Tygh\Addons\ProductVariations\Product\Group\GroupProduct
     */
    public function getProduct()
    {
        return $this->product;
    }
}