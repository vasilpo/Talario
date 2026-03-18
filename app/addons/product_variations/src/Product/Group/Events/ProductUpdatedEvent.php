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

class ProductUpdatedEvent extends AEvent
{
    /** @var \Tygh\Addons\ProductVariations\Product\Group\GroupProduct */
    protected $from;

    /** @var \Tygh\Addons\ProductVariations\Product\Group\GroupProduct */
    protected $to;

    protected function __construct(GroupProduct $from, GroupProduct $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return \Tygh\Addons\ProductVariations\Product\Group\GroupProduct
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return \Tygh\Addons\ProductVariations\Product\Group\GroupProduct
     */
    public function getTo()
    {
        return $this->to;
    }

    public static function create(GroupProduct $from, GroupProduct $to)
    {
        return new self($from, $to);
    }
}